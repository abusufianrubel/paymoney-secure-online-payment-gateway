<?php

namespace Modules\Virtualcard\Http\Controllers\Api;

use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardHolder,
    VirtualcardCategory,
    VirtualcardFeeslimit
};

use Modules\Virtualcard\Http\Resources\{
    VirtuacardResource,
    VirtuacardHolderResource,
};
use Exception;
use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Modules\Virtualcard\Events\VirtualcardRequest;
use Modules\Virtualcard\Http\Requests\User\StoreVirtualcardRequest;

class VirtualcardRequestApiController extends Controller
{
    public function store(StoreVirtualcardRequest $request)
    {
        try {
            $virtualcard = Virtualcard::whereHas('virtualcardHolder', function($query) {
                                $query->where('user_id', auth()->id());
                            })
                            ->where('status', 'Approved')
                            ->get();

            if ($virtualcard->count() >= preference('card_limit')) {
                return $this->unprocessableResponse([], __('You have reached the maximum limit of virtual cards you can create'));
            }


            $virtualcard = new Virtualcard();
            $virtualcard->virtualcard_holder_id = $request->virtualcardHolderId;
            $virtualcard->virtualcard_category_id = $request->preferredCategory;
            $virtualcard->card_brand = $request->cardBrand;
            $virtualcard->currency_code = $request->preferredCurrency;
            $virtualcard->status = 'Pending';
            $virtualcard->save();

            event(new VirtualcardRequest($virtualcard));
            return $this->successResponse(new VirtuacardResource($virtualcard));

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    protected function currency()
    {
        try {
            $currencyCodes = VirtualcardFeeslimit::whereStatus('Active')->get(['virtualcard_currency_code'])->pluck('virtualcard_currency_code')->toArray();
            if (empty($currencyCodes)) {
                return $this->notFoundResponse(__("Currency fees limit is not found"));
            }
            $currencies = Currency::whereIn('code', $currencyCodes)->get(['id', 'code']);
            return $this->successResponse(['currencies' => $currencies]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    protected function cardHolder()
    {
        try {
            $cardholders = VirtualcardHolder::with('user')->where(['user_id' => auth()->id(), 'status' => 'Approved'])->get(['id', 'user_id' , 'address', 'city', 'postal_code', 'card_holder_type', 'business_name', 'first_name', 'last_name']);
            return $this->successResponse(['holders' => VirtuacardHolderResource::collection($cardholders)]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    protected function category()
    {
        try {
            $categories = VirtualcardCategory::whereStatus('active')->get(['id', 'name']);
            return $this->successResponse(['categories' => $categories]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    protected function network()
    {
        try {
            $networks = \Modules\Virtualcard\Enums\CardBrands::cases();
            return $this->successResponse(['networks' => $networks]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function creationFee(Request $request)
    {
        // Return if card holder not found
        $cardHolder = VirtualcardHolder::find($request->cardHolderId, ['id', 'virtualcard_provider_id']);

        if (empty($cardHolder)) {
            return $this->unprocessableResponse([], __('Invalid cardholder ID.'));
        }

        // Return if feeslimit not found
        $creationFee = VirtualcardFeeslimit::where([
                        'virtualcard_currency_code' => $request->preferredCurrency,
                        'virtualcard_provider_id' => $cardHolder->virtualcard_provider_id,
                        'status' => 'Active',
                    ])->max('card_creation_fee');

        if (!$creationFee) {
            return $this->successResponse(['success' => false, 'message' => __('card creation fee not found for this currency.')]);
        }

        // Return if currency not found
        $currency = Currency::where('code', $request->preferredCurrency)->first();

        if (empty($currency)) {
            return $this->successResponse(['success' => false, 'message' => __('Invalid currency.')]);
        }

        // Return if user wallet not exists
        $wallet = Wallet::where(['user_id' => auth()->id(), 'currency_id' => $currency->id])->first();

        if (empty($wallet)) {
            return $this->successResponse(['success' => false, 'message' => __('You do not have a wallet for this currency')]);
        }

        // Return if user wallet balance is less than card creation fee
        if ($wallet->balance < $creationFee) {
            return $this->successResponse(['success' => false, 'message' => __('Plese keep at least :x in your wallet to create a virtual card', ['x' => moneyFormat($currency->symbol, formatNumber($creationFee, $currency->id))])]);
        }

        if ($creationFee && $currency) {
           return $this->successResponse(['success' => true, 'message' => __('A processing fee of :x applies to card generation; it may vary.', ['x' => moneyFormat($currency->symbol, formatNumber($creationFee, $currency->id))])]);
        }
    }
}
