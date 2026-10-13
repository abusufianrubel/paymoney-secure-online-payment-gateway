<?php

namespace Modules\Virtualcard\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TopupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'feesFixed'                 => $this->charge_fixed,
            'feesPercentage'            => $this->charge_percentage,
            'totalFees'                 => $this->total_fees,
            'amount'                    => $this->amount,
            'totalAmount'               => $this->total_amount,
            'formattedFeesFixed'        => formatNumber($this->charge_fixed, $this->currency_id),
            'formattedFeesPercentage'   => formatNumber($this->charge_percentage, $this->currency_id) . '%',
            'formattedTotalFees'        => formatNumber($this->total_fees, $this->currency_id),
            'formattedAmount'           => formatNumber($this->amount, $this->currency_id),
            'formattedTotalAmount'      => formatNumber($this->total_amount, $this->currency_id),
            'currencyId'                => $this->currency_id,
            'virtualCardId'             => $this->virtual_card_id,
            'cardNumber'                => $this->card_number,
            'cardTitle'                 => $this->cardTitle,
            'cardBrand'                 => $this->cardBrand,
            'currencyType'              => optional($this->currency)->type,
            'currencyCode'              => optional($this->currency)->code,
            'currencySymbol'            => optional($this->currency)->symbol
        ];
    }
}
