<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardHolder,
    VirtualcardCategory,
    VirtualcardProvider,
    VirtualcardFeeslimit
};
use Exception, Common, DB;
use App\Models\Wallet;
use App\Models\Currency;
use App\Http\Controllers\Controller;
use Modules\Virtualcard\Responses\CardResponse;
use Modules\Virtualcard\Events\VirtualcardStatusUpdate;
use Modules\Virtualcard\Actions\UpsertVirtualcardAction;
use Modules\Virtualcard\Facades\VirtualcardProviderManager;
use Modules\Virtualcard\DataTransferObject\VirtualcardData;
use Modules\Virtualcard\Events\VirtualcardApplicationIssue;
use Modules\Virtualcard\Http\Requests\Admin\IssueVirtualcardRequest;

class VirtualcardIssueController extends Controller
{
    public function __construct(

        private readonly UpsertVirtualcardAction $virtualcardAction

    ) {}

    public function decline(Virtualcard $virtualcard)
    {
        $cardHolder = VirtualcardHolder::find($virtualcard->virtualcard_holder_id, ['id', 'virtualcard_provider_id']);

        if (!empty($cardHolder)) {
            $creationFee = VirtualcardFeeslimit::where([
                            'virtualcard_currency_code' => $virtualcard->currency_code,
                            'virtualcard_provider_id' => $cardHolder->virtualcard_provider_id,
                            'status' => 'Active',
                        ])->max('card_creation_fee');

            $wallet = Wallet::where(['user_id' => auth()->id(), 'currency_id' => currencyDetails($virtualcard->currency_code)->id])->first(['id', 'balance']);
            if ($wallet) {
                $wallet->increment('balance', $creationFee);
            }
        }

        $virtualcard->status = 'Declined';
        $virtualcard->save();
        event(new VirtualcardStatusUpdate($virtualcard));

        (new Common)->one_time_message('success', __('The :x has been successfully :y.', ['x' => __('card'), 'y' => __('declined')]));
        return redirect()->route('admin.virtualcard.index');
    }

    public function approve(VirtualcardHolder $cardHolder, VirtualcardProvider $provider, Virtualcard $virtualcard)
    {
        try {
            DB::beginTransaction();

            if ($provider->type == 'Automated' && checkDemoEnvironment()) {
                DB::rollBack();
                (new Common)->one_time_message('warning', __('Automated virtualcard approval is not allowed on the demo site.'));
                return redirect()->back();
            }

            // Extract caard creation fee from the user's wallet
            $feesLimit = VirtualcardFeeslimit::where(['virtualcard_provider_id' => $provider->id, 'virtualcard_currency_code' => $virtualcard->currency_code])->first(['id', 'card_creation_fee']);

            $currency = Currency::where('code', $virtualcard->currency_code)->first(['id', 'code']);

            if (empty($virtualcard->virtualcardHolder)) {
                DB::rollBack();
                (new Common)->one_time_message('success', __('Virtualcard holder not found.'));
                return redirect()->route('admin.virtualcard.show', $virtualcard);
            }

            $userWallet = Wallet::where(['user_id' => $virtualcard->virtualcardHolder->user_id, 'currency_id' => $currency->id])->first();

            if (empty($userWallet)) {
                DB::rollBack();
                (new Common)->one_time_message('success', __(':x wallet not exists.', ['x' => $virtualcard->currency_code]));
                return redirect()->route('admin.virtualcard.show', $virtualcard);
            }

            if ($userWallet->balance < $feesLimit->card_creation_fee) {
                DB::rollBack();
                (new Common)->one_time_message('success', __(':x wallet balance is not sufficient for card creation fee.', ['x' => $virtualcard->currency_code]));
                return redirect()->route('admin.virtualcard.show', $virtualcard);
            }

            if ($provider->type == 'Manual') {
                if (!m_g_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU')) {
                    return view('addons::install', ['module' => 'TUFOVUFMVklSVFVBTENBUkRfU0VDUkVU']);
                }
                return redirect()->route('admin.virtualcard_issue.create', [$cardHolder, $provider, $virtualcard]);
            } else {
                if (!m_g_c_v('U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU') && m_aic_c_v('U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU')) {
                    return view('addons::install', ['module' => 'U1RSSVBFVklSVFVBTENBUkRfU0VDUkVU']);
                }
                $providerHandler = VirtualcardProviderManager::get($provider->name);
                $cardService = $providerHandler->getCardService();
                if (!isset($cardService->stripe)) {
                    throw new Exception(__('Please check your provider keys'));
                }
                $cardResponse = $cardService->createVirtualcard($cardHolder->toArray(), $virtualcard->toArray());
            }

            if (! $cardResponse instanceof CardResponse) {
                throw new Exception(__('Invalid card response: Expected an instance of Modules\Virtualcard\Responses\CardResponse, but received :x. Please verify that the card service is returning the correct data structure.', ['x' => get_class($cardResponse)]));
            }

            $virtualcard->virtualcard_provider_id  = $provider->id;
            $virtualcard->card_brand = $cardResponse->cardBrand?->value;
            $virtualcard->card_number = $cardResponse->cardNumber;
            $virtualcard->currency_code = strtoupper($cardResponse->currencyCode);
            $virtualcard->expiry_month = $cardResponse->expiryMonth;
            $virtualcard->expiry_year = $cardResponse->expiryYear;
            $virtualcard->status = $cardResponse->status;
            $virtualcard->updated_at = $cardResponse->createdAt;
            $virtualcard->api_card_id = $cardResponse->apiCardId;
            $virtualcard->api_card_response = $cardResponse->apiCardResponse;
            $virtualcard->save();

            if ($userWallet) {
                $userWallet->balance = $userWallet->balance - $feesLimit->card_creation_fee;
                $userWallet->save();
            }

            DB::commit();

            (new Common)->one_time_message('success', __('Virtual card has successfully approved.'));
            return redirect()->route('admin.virtualcard.show', $virtualcard);

        } catch (Exception $e) {
            (new Common)->one_time_message('error', $e->getMessage());
            return redirect()->route('admin.virtualcard.show', $virtualcard);
        }
    }

    public function create(VirtualcardHolder $cardHolder, VirtualcardProvider $provider, Virtualcard $virtualcard)
    {
        // Check if the virtual card is already approved or declined
        if ($virtualcard->status == 'Approved' || $virtualcard->status == 'Declined') {
            (new Common)->one_time_message('error', __('This card is already :x.', ['x' => $virtualcard->status]));
            return redirect()->back();
        }

        // Get all the approved virtual cards of the user
        $virtualcards = Virtualcard::whereHas('virtualcardHolder', function($query) use ($virtualcard) {
            $query->where('user_id', $virtualcard->virtualcardHolder?->user_id);
        })
        ->where('status', 'Approved')
        ->get();

        // Check if the user has reached the maximum limit of virtual cards
        if ($virtualcards->count() >= preference('card_limit')) {
            (new Common)->one_time_message('error', __('The user reached the maximum limit of virtual cards.'));
            return redirect()->back();
        }

        $currencyIds = json_decode($provider->currency_id);
        $currencies  = Currency::whereIn('id', $currencyIds)->get(['id', 'code', 'type']);

        return view('virtualcard::admin.virtualcard_issues.create', [

            'menu' => 'virtualcard',
            'subMenu' => 'virtualcard',
            'virtualcard' => $virtualcard,
            'virtualcardProvider' => $provider,
            'virtualcardHolder' => $cardHolder,
            'currencies' => $currencies,
            'virtualcardCategories' => VirtualcardCategory::whereStatus('Active')->get()

        ]);
    }

    public function issue(VirtualcardHolder $cardHolder, VirtualcardProvider $provider, Virtualcard $virtualcard, IssueVirtualcardRequest $request)
    {
        try {

            DB::beginTransaction();

            $virtualcardData = VirtualcardData::fromRequest($request);

            if ($virtualcardData->status == 'Approved' || $virtualcardData->status == 'Declined') {
                throw new Exception(__('This virtualcard is already :x.', ['x' => $virtualcardData->virtualcardHolder?->status]));
            }

            $virtualcard = $this->virtualcardAction->execute($virtualcard, $virtualcardData);
            $virtualcard->status = 'Issued';

            event(new VirtualcardApplicationIssue($virtualcard));

            DB::commit();

            (new Common)->one_time_message('success', __('The :x has been successfully issued.', ['x' => __('card')]));
            return redirect()->route('admin.virtualcard.show', $virtualcard);

        } catch (Exception $e) {

            DB::rollBack();
            (new Common)->one_time_message('error', $e->getMessage());
            return back()->withInput();

        }
    }
}
