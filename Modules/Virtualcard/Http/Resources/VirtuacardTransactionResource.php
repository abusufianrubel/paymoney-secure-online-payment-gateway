<?php

namespace Modules\Virtualcard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VirtuacardTransactionResource extends JsonResource
{
    public function toArray($request)
    {

        if ($this->transaction_type_id == Virtualcard_Topup) {
            $transaction = [
                'Virtualcard_Topup' => [
                    "id" => $this->virtualcardTopup->id,
                    "virtualcard_id" => $this->virtualcardTopup->virtualcard_id,
                    "user_id" => $this->virtualcardTopup->user_id,
                    "requested_fund" => formatNumber($this->virtualcardTopup->requested_fund),
                    "percentage" => formatNumber($this->virtualcardTopup->percentage),
                    "percentage_fees" => formatNumber($this->virtualcardTopup->percentage_fees),
                    "fixed_fees" => formatNumber($this->virtualcardTopup->fixed_fees),
                    'total_fees' => formatNumber($this->virtualcardTopup->percentage_fees + $this->virtualcardTopup->fixed_fees),
                    "fund_request_time" => dateFormat($this->virtualcardTopup->fund_request_time),
                    "fund_release_time" => dateFormat($this->virtualcardTopup->fund_release_time),
                    "fund_approval_status" => $this->virtualcardTopup->fund_approval_status,
                    "cancellation_reason" => $this->virtualcardTopup->cancellation_reason ?? null,
                    "created_at" => dateFormat($this->virtualcardTopup->created_at),
                    "updated_at" => dateFormat($this->virtualcardTopup->updated_at)
                ]
            ] ;
        } else {
            $transaction = [
                'Virtualcard_Withdrawal'    => [
                    "id" => $this->virtualcardWithdrawal->id,
                    "virtualcard_id" => $this->virtualcardWithdrawal->virtualcard_id,
                    "user_id" => $this->virtualcardWithdrawal->user_id,
                    "requested_fund" => formatNumber($this->virtualcardWithdrawal->requested_fund),
                    "percentage" => formatNumber($this->virtualcardWithdrawal->percentage),
                    "percentage_fees" => formatNumber($this->virtualcardWithdrawal->percentage_fees),
                    "fixed_fees" => formatNumber($this->virtualcardWithdrawal->fixed_fees),
                    'total_fees' => formatNumber($this->virtualcardWithdrawal->fixed_fees + $this->virtualcardWithdrawal->percentage_fees),
                    "fund_request_time" => dateFormat($this->virtualcardWithdrawal->fund_request_time),
                    "fund_release_time" => dateFormat($this->virtualcardWithdrawal->fund_release_time),
                    "fund_approval_status" => $this->virtualcardWithdrawal->fund_approval_status,
                    "cancellation_reason" => $this->virtualcardWithdrawal->cancellation_reason ?? null,
                    "created_at" => dateFormat($this->virtualcardWithdrawal->created_at),
                    "updated_at" => dateFormat($this->virtualcardWithdrawal->updated_at)
                ] 
            ];
        }

        $coreTransaction = [
            'id'                        => $this->id,
            'user'                      => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ],
            'currency'                  => [
                'id' => $this->currency->id,
                'symbol' => $this->currency->symbol,
                'code' => $this->currency->code
            ],
            'payment_method'            => [
                'id' => $this->payment_method->id,
                'name' => $this->payment_method->name
            ],
            'uuid'                      => $this->uuid,
            'transaction_reference_id'  => $this->transaction_reference_id,
            'transaction_type'          => $this->transaction_type,
            'user_type'                 => $this->user_type,
            'email'                     => $this->email,
            'phone'                     => $this->phone,
            'subtotal'                  => formatNumber($this->subtotal),
            'percentage'                => formatNumber($this->percentage),
            'charge_percentage'         => formatNumber($this->charge_percentage),
            'charge_fixed'              => formatNumber($this->charge_fixed),
            'total'                     => formatNumber($this->total),
            'note'                      => $this->note,
            'payment_status'            => $this->payment_status,
            'status'                    => $this->status,
            'created_at'                => dateFormat($this->created_at),
            'updated_at'                => dateFormat($this->updated_at),
        ];

        return array_merge($coreTransaction, $transaction);

    }

}
