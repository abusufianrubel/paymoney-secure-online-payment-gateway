<?php

namespace Modules\Virtualcard\Actions;

use Exception;
use Carbon\Carbon;
use App\Models\Transaction;
use Modules\Virtualcard\Entities\VirtualcardProvider;
use Modules\Virtualcard\Entities\VirtualcardFeeslimit;
use Modules\Virtualcard\Entities\VirtualcardWithdrawal;
use Modules\Virtualcard\DataTransferObject\VirtualcardWithdrawaData;

class CreateVirtualcardWithdrawalAction
{

    public function execute(VirtualcardWithdrawaData $virtualcardWithdrawalData): VirtualcardWithdrawal
    {
        $fees = $this->checkVirtualcardWithdrawalDataValidity($virtualcardWithdrawalData);

        # Withdrawal
        $virtualcardWithdrawal = new VirtualcardWithdrawal();
        $virtualcardWithdrawal->virtualcard_id = $virtualcardWithdrawalData->virtualcard?->id;
        $virtualcardWithdrawal->user_id = $virtualcardWithdrawalData->user?->id;
        $virtualcardWithdrawal->requested_fund = $virtualcardWithdrawalData->requestedFund;
        $virtualcardWithdrawal->percentage = $fees->percentage;
        $virtualcardWithdrawal->percentage_fees = $fees->percentageFee;
        $virtualcardWithdrawal->fixed_fees = $fees->fixedFee;
        $virtualcardWithdrawal->fund_request_time = Carbon::now();
        $virtualcardWithdrawal->fund_release_time = $virtualcardWithdrawalData->fundReleaseTime ?? null;
        $virtualcardWithdrawal->fund_approval_status = $virtualcardWithdrawalData->fundApprovalStatus ?? 'Pending';
        $virtualcardWithdrawal->cancellation_reason = $virtualcardWithdrawalData->cancellationReason;
        $virtualcardWithdrawal->save();

        # Core Transaction
        $transaction = new Transaction;
        $transaction->user_id = $virtualcardWithdrawalData->user?->id;
        $transaction->currency_id = $virtualcardWithdrawalData->wallet?->currency?->id;
        $transaction->payment_method_id = 1;
        $transaction->uuid = uniqid();
        $transaction->transaction_reference_id = $virtualcardWithdrawal->id;
        $transaction->transaction_type_id = Virtualcard_Withdrawal;
        $transaction->subtotal = $virtualcardWithdrawalData->requestedFund;
        $transaction->percentage = $fees->percentage;
        $transaction->charge_percentage = $fees->percentageFee;
        $transaction->charge_fixed = $fees->fixedFee;
        $transaction->total =  $virtualcardWithdrawalData->requestedFund + ($fees->totalFee);
        $transaction->status = 'Pending';
        $transaction->save();

        # Virtualcard
        $virtualcard = $virtualcardWithdrawalData->virtualcard;
        $virtualcard->amount -= ($virtualcardWithdrawalData->requestedFund + $fees->totalFee);
        $virtualcard->save();

        return $virtualcardWithdrawal;
    }

    public function checkVirtualcardWithdrawalDataValidity(VirtualcardWithdrawaData $virtualcardWithdrawalData)
    {
        $this->checkVirtualcardValidity($virtualcardWithdrawalData);
        $this->checkVirtualcardAmountValidity($virtualcardWithdrawalData);
        return $this->withdrawalFee(
            $virtualcardWithdrawalData->virtualcard?->virtualcardProvider,
            $virtualcardWithdrawalData->virtualcard?->currency_code,
            $virtualcardWithdrawalData->requestedFund,
        );
    }


    public function checkVirtualcardValidity(VirtualcardWithdrawaData $virtualcardWithdrawaData)
    {
         // Card Validation
         if ($virtualcardWithdrawaData->virtualcard?->virtualcardHolder?->user_id != auth()->id()) {
            throw new Exception(__('You are not authorized to use this card.'));
        }

        if (Carbon::now() > Carbon::createFromDate(
            $virtualcardWithdrawaData->virtualcard?->expiry_year,
            $virtualcardWithdrawaData->virtualcard?->expiry_month,
            1
        )->endOfMonth()) {
            throw new Exception(__('The card is no longer valid due to expiration.'));
        }

        if ($virtualcardWithdrawaData->virtualcard?->status != 'Active') {
            throw new Exception(__('Please select an eligible card for withdrawal.'));
        }

        if ($virtualcardWithdrawaData->virtualcard?->currency_code != $virtualcardWithdrawaData->wallet?->currency?->code) {
            throw new Exception(__('The selected currency is not supported by this card.'));
        }
    }

    public function checkVirtualcardAmountValidity(VirtualcardWithdrawaData $virtualcardWithdrawaData)
    {
        # Fees
        $feesLimit = $this->withdrawalFee(
                                $virtualcardWithdrawaData->virtualcard?->virtualcardProvider,
                                $virtualcardWithdrawaData->virtualcard?->currency_code,
                                $virtualcardWithdrawaData->requestedFund
                            );

        // # Min Withdrawal Limit
        if (($virtualcardWithdrawaData->requestedFund) < $feesLimit->withdrawalMinLimit) {
            throw new Exception(__('Your minimum withdrawal limit is :x', ['x' => moneyFormat($virtualcardWithdrawaData->virtualcard?->currency()?->symbol, formatNumber($feesLimit->withdrawalMinLimit, $virtualcardWithdrawaData->virtualcard?->currency()?->id))]));
        }

        # Max Withdrawal Limit
        if (($virtualcardWithdrawaData->requestedFund) > $feesLimit->withdrawalMaxLimit) {
            throw new Exception(__('Your maximum withdrawal limit is :x', ['x' => moneyFormat($virtualcardWithdrawaData->virtualcard?->currency()?->symbol, formatNumber($feesLimit->withdrawalMaxLimit, $virtualcardWithdrawaData->virtualcard?->currency()?->id))]));
        }

        # Insufficient Balance
        if (($virtualcardWithdrawaData->requestedFund + $feesLimit->totalFee) > $virtualcardWithdrawaData->virtualcard?->amount) {
            throw new Exception(__('Insufficient funds.'));
        }
    }

    public function withdrawalFee(VirtualcardProvider $provider, string $code, float $amount)
    {
        $feesLimit = VirtualcardFeeslimit::where(['virtualcard_currency_code' => $code, 'virtualcard_provider_id' => $provider->id, 'status' => 'Active'])->first();
        if (empty($feesLimit)) {
                throw new Exception(__("Fees limit not set for this currency"));
        }
        $percentageFee = ($feesLimit->withdrawal_percentage_fee * $amount) / 100;
        $fixedFee = $feesLimit->withdrawal_fixed_fee;
        $withdrawalMinLimit = $feesLimit->withdrawal_min_limit;
        $withdrawalMaxLimit = $feesLimit->withdrawal_max_limit;

        return (object)[
            'percentage' => $feesLimit->withdrawal_percentage_fee,
            'percentageFee' => $percentageFee,
            'fixedFee' => $fixedFee,
            'totalFee' => $percentageFee + $fixedFee,
            'withdrawalMinLimit' => $withdrawalMinLimit,
            'withdrawalMaxLimit' => $withdrawalMaxLimit
        ];

    }
}
