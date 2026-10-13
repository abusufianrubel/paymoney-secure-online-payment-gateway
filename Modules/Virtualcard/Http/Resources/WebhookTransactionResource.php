<?php

namespace Modules\Virtualcard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'virtualcard_id' => $this->virtualcard_id,
            'user' => [
                'id' => $this->user->id ?? null,
                'first_name' => $this->user->first_name ?? null,
                'last_name' => $this->user->last_name ?? null
            ],
            'amount' => formatNumber($this->amount),
            'currency' => $this->currency,
            'card_number' => $this->card_number ? maskCardNumberForLogo($this->card_number) : null,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'created_at' => dateFormat($this->created_at)
        ];
    }
}

