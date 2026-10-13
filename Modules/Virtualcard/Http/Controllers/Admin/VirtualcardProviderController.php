<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use DB, Exception;
use App\Models\Currency;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Modules\Virtualcard\Entities\VirtualcardProvider;
use Modules\Virtualcard\Entities\VirtualcardFeeslimit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Virtualcard\DataTables\VirtualcardProviderDataTable;
use Modules\Virtualcard\Facades\VirtualcardProviderManager;
use Modules\Virtualcard\Http\Requests\Admin\UpsertVirtualcardProviderRequest;

class VirtualcardProviderController extends Controller
{
    public function index(VirtualcardProviderDataTable $dataTable)
    {
        return $dataTable->render('virtualcard::admin.virtualcard_providers.index', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'provider'

        ]);
    }

    /**
     * Show the form for creating a new virtual card provider.
     */
    public function create()
    {
        if (!m_g_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU')) {
            return view('addons::install', ['module' => 'TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU']);
        }
        return view('virtualcard::admin.virtualcard_providers.create', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'provider',
            'activeCurrency' => Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'status', 'code', 'type', 'default'])

        ]);
    }

    /**
     * Store a newly created virtual card provider in storage.
     */
    public function store(UpsertVirtualcardProviderRequest $request)
    {
        
        if (!isActive('ManualVirtualcard')) {
            Common::one_time_message('error', __('Virtualcard manual provider is not active'));
            return redirect()->route('admin.virtualcard_provider.index');
        }

        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['currency_id'] = json_encode($validatedData['currency_id']);

            VirtualcardProvider::create($validatedData);

            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully saved.', ['x' => __('provider')]));
            return redirect()->route('admin.virtualcard_provider.index');

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());
            return redirect()->route('admin.virtualcard_provider.index');

        }
    }

    /**
     * Show the form for editing the specified virtual card provider.
     */
    public function edit(VirtualcardProvider $virtualcardProvider)
    {
        return view('virtualcard::admin.virtualcard_providers.edit', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'provider',
            'provider' => $virtualcardProvider,
            'activeCurrency' => Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'status', 'code', 'type', 'default'])

        ]);
    }

    /**
     * Update the specified virtual card provider in storage.
     */
    public function update(VirtualcardProvider $provider, UpsertVirtualcardProviderRequest $request)
    {
        try {
            if ($provider->type == 'Automated') {
                if (!m_g_c_v('U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU')) {
                    return view('addons::install', ['module' => 'U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU']);
                }
            }
            if ($provider->type == 'Manual') {
                if (!m_g_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU')) {
                    return view('addons::install', ['module' => 'TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU']);
                }
            }

            $previousCurrency = json_decode($provider->currency_id, true);
            $newCurrency = $request->currency_id;

            $uncommonProviderCurrency = array_merge(array_diff($previousCurrency, $newCurrency), array_diff($newCurrency, $previousCurrency));
            $providerCurrencyCode = [];
            if ($uncommonProviderCurrency) {
                foreach ($uncommonProviderCurrency as $value) {
                    $providerCurrencyCode[] = Currency::find($value)->code;
                }
                $virtualcardFees = VirtualcardFeeslimit::where('virtualcard_provider_id', $provider->id)->whereIn('virtualcard_currency_code', $providerCurrencyCode)->count();

                if (!empty($virtualcardFees > 0) ) {
                    Common::one_time_message('error', __('Cannot update. Removed currency already used in fees limits.'));
                    return redirect()->route('admin.virtualcard_provider.edit', $provider->id);
                }
            }
            DB::beginTransaction();

            $validatedData = $request->validated();

            if ($provider->type == 'Automated') {
                unset($validatedData['name']);
            }

            $validatedData['currency_id'] = json_encode($validatedData['currency_id']);
            $provider->update($validatedData);

            DB::commit();

            // Setting up webhook for automated provider
            if ($provider->type == 'Automated') {
                $data['url'] = route('virtualcard.webhook', ['provider' => strtolower($provider->name)]);
                $providerHandler = VirtualcardProviderManager::get($provider->name);
                $cardWebhookSetupService = $providerHandler->getWebhookService();
                if (!isset($cardWebhookSetupService->stripe)) {
                    throw new Exception(__('Please check your provider keys'));
                }
                $cardWebhookSetupResponse = $cardWebhookSetupService->webhookSetup($data);
            }

            Common::one_time_message('success', __('The :x has been successfully saved.', ['x' => __('Provider')]));
            return redirect()->route('admin.virtualcard_provider.index');

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());
            return redirect()->route('admin.virtualcard_provider.index');

        }
    }

    /**
     * Remove the specified virtual card provider from storage.
     */
    public function destroy($id)
    {
        try {

            $virtualcardFees = VirtualcardFeeslimit::where(['virtualcard_provider_id'=> $id])->first();

            if (!empty($virtualcardFees)) {
                Common::one_time_message('error', __('Cannot delete. This provider is already used in a fees limit.'));
                return redirect()->route('admin.virtualcard_provider.index');
            }
            DB::beginTransaction();

            $virtualcardProvider = VirtualcardProvider::findOrFail($id);

            if (checkDemoEnvironment()) {
                Common::one_time_message('warning', __('Virtualcard provider delete is not allowed on the demo site.'));
                return redirect()->back();
            }
            
            if ($virtualcardProvider->type == 'Automated') {
                Common::one_time_message('error', __('Automated provider can not be deleted.'));
                return redirect()->route('admin.virtualcard_provider.index');
            }

            $virtualcardProvider->delete();

            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully deleted.', ['x' => __('provider')]));

        } catch (ModelNotFoundException $ex) {

            DB::rollback();
            Common::one_time_message('error', __('Requested data has not been found!'));

        } catch (QueryException $ex) {

            DB::rollback();
            Common::one_time_message('error', __('Unable to delete because this provider is linked to a virtual card.'));

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());

        }

        return redirect()->route('admin.virtualcard_provider.index');
    }

    public function getCurrency($virtualCardProviderId)
    {
        $virtualCardProvider = VirtualcardProvider::find($virtualCardProviderId);

        $currencyIds = json_decode($virtualCardProvider->currency_id);

        $feesLimitCurrencyCodes = VirtualcardFeeslimit::where('virtualcard_provider_id', $virtualCardProviderId)
            ->pluck('virtualcard_currency_code')
            ->toArray();

        $providerCurrencyCodes = Currency::whereIn('id', $currencyIds)
            ->where(['type' => 'fiat', 'status' => 'Active'])
            ->pluck('code')
            ->toArray();

        // Find common currencies between the two sets
        $commonCurrencyCodes = array_intersect($feesLimitCurrencyCodes, $providerCurrencyCodes);

        $commonCurrencies = Currency::whereIn('code', $commonCurrencyCodes)
            ->where(['type' => 'fiat', 'status' => 'Active'])
            ->get(['id', 'code', 'type']);

        return response()->json([
            'currencies' => $commonCurrencies,
        ]);
    }
}
