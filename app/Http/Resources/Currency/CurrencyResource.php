<?php

namespace App\Http\Resources\Currency;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'symbol'        => $this->symbol,
            'code'          => $this->code,
            'status'        => $this->status,
            'type'          => $this->type,
            'rate'          => $this->rate,
            'default'       => $this->default,
            'logo'          => $this->logo,
            'path'          => image($this->logo, 'currency'),
        ];
    }

    public function with($request)
    {
        return [
            'version' => '1.0',
        ];
    }
}
