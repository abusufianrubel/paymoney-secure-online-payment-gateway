<?php
namespace Modules\Virtualcard\Services;

use App\Models\{
    Currency,
    Wallet,
    Transaction
};
use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardTopup,
    VirtualcardFeeslimit
};
use Carbon\Carbon;
use Common, Exception, DB;
use App\Exceptions\Api\V2\FeesException;
use Modules\Virtualcard\Transformers\TopupResource;

class VirtualcardTopupService
{
    /**
     * Get the currencies list for deposit
     *
     * @return void
     */
    public function getUserVirtualCards()
    {
        $loggedInUserId = auth()->id();
        $currentYear    = Carbon::now()->year;
        $currentMonth   = Carbon::now()->month;
        $virtualCards   = Virtualcard::select('id', 'card_number', 'virtualcard_holder_id', 'card_brand', 'currency_code')
        ->with([
            'virtualcardHolder:id,card_holder_type,business_name,first_name,last_name',
            ])
        ->whereHas('virtualcardHolder', function ($query) use ($loggedInUserId) {
            $query->where('user_id', $loggedInUserId);
        })
        ->where('status', 'Active')
        ->where(function ($query) use ($currentYear, $currentMonth) {
        $query->where('expiry_year', '>', $currentYear)
            ->orWhere(function ($subQuery) use ($currentYear, $currentMonth) {
                $subQuery->where('expiry_year', '=', $currentYear)
                ->where('expiry_month', '>=', $currentMonth);
            });
        })
        ->get();

        $dropdownData = $virtualCards->map(function ($virtualCard) {
            if ($virtualCard->virtualcardHolder) {

                $maskedCardNumber = maskCardNumberForLogo($virtualCard->card_number);
                $cardLable = cardTitle($virtualCard->virtualcardHolder);
                $cardIcon = $virtualCard->card_brand == 'Visa Card' ? virtualcardSvgIcons('visa_card_icon') : virtualcardSvgIcons('master_card_icon');

                return [
                    'id'    => $virtualCard->id,
                    'type' => Currency::where('code', $virtualCard->currency_code)->first(['id', 'type'])->type, 
                    'label' => "{$maskedCardNumber} ({$cardLable})",
                    'cardicon' => $cardIcon
                ];
            }
            return null;
        })->filter();

        return $dropdownData;
    }

    public function validateTopupFeesLimit($amount, $virtualCardId, $topupWallet)
    {
        $virtualcard = Virtualcard::find($virtualCardId);
        if (empty($virtualcard)) {
            throw new Exception(__('Please select an eligible card for topup.'));
        }

        $virtualcardFees = VirtualcardFeeslimit::where(['virtualcard_provider_id'=> $virtualcard->virtualcard_provider_id, 'virtualcard_currency_code' => $topupWallet, 'status' => 'Active'])->first();
        if (is_null($virtualcardFees)) {
            throw new FeesException(__("Fees limit not set for this currency"));
        }

        if (Carbon::now() > Carbon::createFromDate(
            $virtualcard->expiry_year,
            $virtualcard->expiry_month,
            1
        )->endOfMonth()) {
            throw new Exception(__('The card is no longer valid due to expiration.'));
        }

        if ($virtualcard->status != 'Active') {
            throw new Exception(__('Please select an eligible card for topup.'));
        }

        $minError = (float) $amount < $virtualcardFees->topup_min_limit;
        $maxError = $virtualcardFees->topup_max_limit &&  $amount > $virtualcardFees->topup_max_limit;

        if ($minError && $maxError) {
            throw new FeesException(__("Maximum acceptable amount is :x and minimum acceptable amount is :y", [
                "x" => formatNumber($virtualcardFees->topup_max_limit, optional($virtualcardFees->currency)->id),
                "y" => formatNumber($virtualcardFees->topup_min_limit, optional($virtualcardFees->currency)->id),
            ]));
        } elseif ($maxError) {
            throw new FeesException(__(
                "Maximum acceptable amount is :x",
                [
                    "x" => formatNumber($virtualcardFees->topup_max_limit, optional($virtualcardFees->currency)->id)
                ]
            ));
        } elseif ($minError) {
            throw new FeesException(__(
                "Minimum acceptable amount is :x",
                [
                    "x" => formatNumber($virtualcardFees->topup_min_limit, optional($virtualcardFees->currency)->id)
                ]
            ));
        }
        $virtualcardFees->amount        = $amount;
        $virtualcardFees->currency_id   = optional($virtualcardFees->currency)->id;
        $virtualcardFees->charge_fixed  = $virtualcardFees->topup_fixed_fee;
        $virtualcardFees->virtual_card_id   = $virtualcard->id;
        $virtualcardFees->card_number       = maskCardNumberForLogo($virtualcard->card_number);
        $virtualcardFees->charge_percentage = $virtualcardFees->topup_percentage_fee;
        $virtualcardFees->fees_percentage   = $amount * ($virtualcardFees->topup_percentage_fee / 100);
        $virtualcardFees->total_fees        = $virtualcardFees->fees_percentage + $virtualcardFees->topup_fixed_fee;
        $virtualcardFees->total_amount      = $virtualcardFees->total_fees + $amount;
        $virtualcardFees->cardTitle         = cardTitle($virtualcard->virtualcardHolder);
        $virtualcardFees->cardBrand         = $virtualcard->card_brand;
        (new Common())->checkWalletAmount(auth()->id(), $virtualcardFees->currency_id, ($amount + $virtualcardFees->total_fees));

        return (new TopupResource($virtualcardFees))->toArray(request());

    }

    public function getUserSingleVirtualCard($cardId)
    {
        $loggedInUserId = auth()->id();
        $currentYear    = Carbon::now()->year;
        $currentMonth   = Carbon::now()->month;
        $virtualCard    = Virtualcard::select('id', 'card_number', 'virtualcard_holder_id', 'virtualcard_provider_id', 'currency_code', 'card_brand', 'expiry_month', 'expiry_year')
        ->with([
            'virtualcardHolder'
            ])
        ->whereHas('virtualcardHolder', function ($query) use ($loggedInUserId) {
            $query->where('user_id', $loggedInUserId);
        })
        ->where([['id', '=', $cardId]])
        ->whereStatus('Active')
        ->where(function ($query) use ($currentYear, $currentMonth) {
            $query->where('expiry_year', '>', $currentYear)
                  ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                      $query->where('expiry_year', '=', $currentYear)
                            ->where('expiry_month', '>=', $currentMonth);
                  });
        })
        ->first();

        if (!empty($virtualCard)) {
            $maskedCardNumber = maskCardNumberForLogo($virtualCard->card_number);
            $cardTitle = cardTitle( $virtualCard->virtualcardHolder);
            return [
                'cardId'   => $virtualCard->id,
                'wallet'   => $virtualCard->currency_code,
                'type' => Currency::where('code', $virtualCard->currency_code)->first(['id', 'type'])->type, 
                'cardName' => "{$maskedCardNumber} ({$cardTitle})",
                'cardBrand' => $virtualCard->card_brand,
            ];
        }
        return null;
    }

    public function virtualcardTopupTransaction($sessionValue)
    {
        $amount  = $sessionValue['amount'];
        $uuid    = unique_code();
        $userId  = auth()->id();
        $percentFees = $amount * $sessionValue['feesPercentage'] / 100;
        try {

            DB::beginTransaction();
            //virtualcard topup
            $virtualcardTopup                   = new VirtualcardTopup();
            $virtualcardTopup->virtualcard_id   = $sessionValue['virtualCardId'];
            $virtualcardTopup->user_id          = $userId;
            $virtualcardTopup->requested_fund   = $amount;
            $virtualcardTopup->percentage       = $sessionValue['feesPercentage'];
            $virtualcardTopup->percentage_fees  = $percentFees;
            $virtualcardTopup->fixed_fees       = $sessionValue['feesFixed'];
            $virtualcardTopup->fund_request_time    = date('Y-m-d H:i:s');
            $virtualcardTopup->fund_approval_status = 'Pending';
            $virtualcardTopup->cancellation_reason  = '';
            $virtualcardTopup->save();

            //Transaction
            $transaction                    = new Transaction();
            $transaction->user_id           = $userId;
            $transaction->currency_id       = $sessionValue['currencyId'];
            $transaction->payment_method_id = Mts;
            $transaction->transaction_reference_id = $virtualcardTopup->id;
            $transaction->transaction_type_id      = Virtualcard_Topup;
            $transaction->uuid               = $uuid;
            $transaction->subtotal           = $amount;
            $transaction->percentage         = $sessionValue['feesPercentage'];
            $transaction->charge_percentage  = $percentFees;
            $transaction->charge_fixed       = $sessionValue['feesFixed'];
            $transaction->total              = $sessionValue['totalAmount'];
            $transaction->status             = 'Pending';
            $transaction->save();

            //Wallet
            $wallet = Wallet::where(['user_id' => $userId, 'currency_id' => $sessionValue['currencyId']])->first(['id', 'balance']);
            if ($wallet) {
                $wallet->decrement('balance', $transaction->total);
            }
            DB::commit();

            return [
                'gateway'   => 'Wallet',
                'id'        => $virtualcardTopup->id,
                'subtotal'  => $virtualcardTopup->requested_fund,
                'topup'     => $virtualcardTopup,
                'currencyCode' => $sessionValue['currencyCode'],
                'currencyId'   => $sessionValue['currencyId'],
                'currSymbol'   => $sessionValue['currencySymbol'],
            ];
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function getDistinctValues(string $column)
    {
        return VirtualcardTopup::select($column)->distinct()->get();
    }

    public function getDistinctCardValues(string $column)
    {
        return Virtualcard::select($column)->distinct()->get();
    }

    public function topupStatusChange($request, $id)
    {
        try {
            DB::beginTransaction();
            $virtualcardtopup = VirtualcardTopup::select(['id', 'virtualcard_id', 'requested_fund', 'fund_approval_status', 'cancellation_reason', 'user_id'])->with([
                'transaction:id,transaction_reference_id,status,currency_id,total',
                'virtualcard:id,currency_code,amount',
            ])->find($id);
            $wallet = Wallet::where(['user_id' => $virtualcardtopup->user_id, 'currency_id' => optional($virtualcardtopup->transaction)->currency_id])->first(['id', 'balance']);

            if ($virtualcardtopup && $wallet) {
                $status = $request->fund_approval_status;

                if ($virtualcardtopup->fund_approval_status == 'Pending') {
                    if ($status == 'Approved') {
                        $virtualcardtopup->fund_approval_status = 'Approved';
                        $virtualcardtopup->transaction->status  = 'Success';

                        $virtualcardtopup->virtualcard->increment('amount', $virtualcardtopup->requested_fund);
                    } else if ($status == 'Cancelled') {
                        $virtualcardtopup->fund_approval_status = 'Cancelled';
                        $virtualcardtopup->cancellation_reason  = $request->cancellation_reason;

                        $virtualcardtopup->transaction->status  = 'Blocked';

                        $wallet->increment('balance', $virtualcardtopup->transaction?->total);
                    }
                } else if ($virtualcardtopup->fund_approval_status == 'Cancelled') {
                    if ($status == 'Approved') {
                        $virtualcardtopup->fund_approval_status = 'Approved';
                        $virtualcardtopup->transaction->status  = 'Success';

                        $virtualcardtopup->virtualcard->increment('amount', $virtualcardtopup->requested_fund);
                        $wallet->decrement('balance', $virtualcardtopup->transaction?->total);
                    } else if ($status == 'Pending') {
                        $virtualcardtopup->fund_approval_status  = 'Pending';

                        $virtualcardtopup->transaction->status   = 'Pending';

                        $wallet->decrement('balance', $virtualcardtopup->transaction?->total);
                    }
                }

                $virtualcardtopup->save();
                $virtualcardtopup->transaction->save();
            }
            DB::commit();
            return $virtualcardtopup;
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return $e->getMessage();
        }
    }

}
