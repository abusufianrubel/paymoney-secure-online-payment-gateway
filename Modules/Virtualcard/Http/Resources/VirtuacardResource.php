<?php

namespace Modules\Virtualcard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Virtualcard\Entities\VirtualcardSpending;

class VirtuacardResource extends JsonResource
{
    public function toArray($request)
    {
        $totalSpent = VirtualcardSpending::where('virtualcard_id', $this->id)->sum('amount');

        if ($this->virtualcardProvider?->type == 'Automated') {
            $balance = [
                'total_balance' => formatNumber($this->amount),
                'available_balance' => formatNumber($this->amount - $totalSpent),
                'total_spent' => formatNumber($totalSpent)
            ];
        } else {
            $balance = [
                'total_balance' => formatNumber($this->amount)
            ];
        }


        $cardDetails = [
            'id' => $this->id,
            'virtualcard_holder' => $this->virtualcardHolder,
            'virtualcard_category' => [
                'id' => $this->virtualcardCategory->id ?? null,
                'name' => $this->virtualcardCategory->name ?? null
            ],
            'card_brand' => $this->card_brand,
            'card_number' => $this->card_number ? maskCardNumberForLogo($this->card_number) : null,
            'card_cvc' => $this->card_cvc ? str_repeat('*', strlen($this->card_cvc)) : null,
            'card_currency' => $this->currency_code,
            'card_expiry' => $this->expiry_month && $this->expiry_year ? formatCardExpiryDate($this->expiry_month, $this->expiry_year) : null,
            'type' => $this->virtualcardProvider?->type,
            'status' => $this->status,
            'created_at' => dateFormat($this->created_at),
            'updated_at' => dateFormat($this->updated_at),
        ];

        return array_merge($cardDetails, $balance);
    }

}
