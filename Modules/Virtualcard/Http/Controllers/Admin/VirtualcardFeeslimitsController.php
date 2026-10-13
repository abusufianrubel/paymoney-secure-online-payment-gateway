<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardProvider,
    VirtualcardFeeslimit
};
use Illuminate\Database\{
    QueryException,
    Eloquent\ModelNotFoundException
};
use Modules\Virtualcard\{
    DataTables\VirtualcardFeesLimitsDataTable,
    Http\Requests\Admin\StoreVirtualcardFeeslimitsRequest,
    Http\Requests\Admin\UpdateVirtualcardFeesLimitRequest
};
use App\Models\Currency;
use Exception, DB, Common;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VirtualcardFeeslimitsController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    /**
     * Display a listing of the Card Categories.
     * @return Renderable
     */
    public function index(VirtualcardFeesLimitsDataTable $dataTable)
    {
        return $dataTable->render('virtualcard::admin.virtualcard_fees.index', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'card_fees'

        ]);
    }

    /**
     * Show the form for creating a new Card Category.
     * @return Renderable
     */
    public function create()
    {
        return view('virtualcard::admin.virtualcard_fees.create', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'card_fees',
            'cardProvider' => VirtualcardProvider::where(['status' => 'Active'])->get(['id', 'name'])

        ]);
    }

    /**
     * Store a newly created Card Category in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreVirtualcardFeeslimitsRequest $request)
    {
        try {

            DB::beginTransaction();

            $validatedData = $request->validated();
            $provider = VirtualcardProvider::find($validatedData['virtualcard_provider_id']);

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
            VirtualcardFeeslimit::create($validatedData);

            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully saved.', ['x' => __('Card Feeslimits')]));
            return redirect()->route('admin.card_fees.index');

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());
            return redirect()->route('admin.card_fees.index');

        }
    }

    /**
     * Show the form for editing the specified Card Category
     * @param int $id

     */
    public function edit(VirtualcardFeeslimit $virtualcardFeeslimit)
    {
        $currencyType = Currency::find(settings('default_currency'))->value('type');

        return view('virtualcard::admin.virtualcard_fees.edit', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'card_fees',
            'currencyType'  => ($currencyType == 'fiat') ? preference('decimal_format_amount', 2) : preference('decimal_format_amount_crypto', 8),
            'cardFeesLimit' => $virtualcardFeeslimit,
            'cardProvider'  => VirtualcardProvider::where(['status' => 'Active'])->get(['id', 'name'])

        ]);

    }

    /**
     * Update the specified Card Category in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateVirtualcardFeesLimitRequest $request, VirtualcardFeeslimit $virtualcardFeeslimit)
    {
        try {

            DB::beginTransaction();
            $validatedData = $request->validated();

            $provider = VirtualcardProvider::find($validatedData['virtualcard_provider_id']);

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
            $virtualcard = Virtualcard::where(['virtualcard_provider_id'=> $virtualcardFeeslimit->virtualcard_provider_id, 'currency_code' => $virtualcardFeeslimit->virtualcard_currency_code])->first();

            if (
                ($request->virtualcard_currency_code != $virtualcardFeeslimit->virtualcard_currency_code || $virtualcardFeeslimit->virtualcard_provider_id != $request->virtualcard_provider_id) &&
                !empty($virtualcard)
            ) {
                Common::one_time_message('error', __('Cannot update. This fees limit is already used in a virtual card.'));
                return redirect()->route('admin.card_fees.edit', $virtualcardFeeslimit->id);
            }

            $virtualcardFeeslimit->update($validatedData);

            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully saved.', ['x' => __('Card Feeslimits')]));
            return redirect()->route('admin.card_fees.index');

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());
            return redirect()->route('admin.card_fees.index');

        }
    }

    /**
     * Remove the specified Card Category from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(VirtualcardFeeslimit $virtualcardFeeslimit)
    {
        try {

            $virtualcard = Virtualcard::where(['virtualcard_provider_id'=> $virtualcardFeeslimit->virtualcard_provider_id, 'currency_code' => $virtualcardFeeslimit->virtualcard_currency_code])->first();

            if (optional($virtualcardFeeslimit->cardProvider)->type == 'Automated' && checkDemoEnvironment()) {
                Common::one_time_message('warning', __('Automated virtualcard fees limit delete is not allowed on the demo site.'));
                return redirect()->back();
            }

            if (!empty($virtualcard)) {
                Common::one_time_message('error', __('Cannot delete. This fees limit is already used in a virtual card.'));
                return redirect()->route('admin.card_fees.index');
            }
            DB::beginTransaction();

            $virtualcardFeeslimit->delete();
            DB::commit();
            Common::one_time_message('success', __('The :x has been successfully deleted.', ['x' => __('Card Feeslimits')]));

        } catch (ModelNotFoundException $ex) {

            DB::rollback();
            Common::one_time_message('error', __('Requested data has not been found!'));

        } catch (QueryException $ex) {

            DB::rollback();
            Common::one_time_message('error', __('Cannot delete. This fees limit is already used in transaction.'));

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());

        }

        return redirect()->route('admin.card_fees.index');

    }

    public function getProviderCurrency($virtualCardProviderId)
    {
        $virtualCardProvider = VirtualcardProvider::where('id', $virtualCardProviderId)->first();
        $currencyIds = json_decode($virtualCardProvider->currency_id);
        $currencies = Currency::whereIn('id', $currencyIds)->get(['id', 'code']);

        return response()->json([
            'currencies' => $currencies
        ]);
    }
}

