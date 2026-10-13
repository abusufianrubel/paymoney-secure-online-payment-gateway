<?php

namespace Modules\Virtualcard\Entities;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardFeeslimit extends Model
{
    use HasFactory;

    protected $fillable = ['virtualcard_provider_id', 'virtualcard_currency_code', 'card_creation_fee', 'topup_max_limit', 'topup_min_limit', 'topup_fixed_fee', 'topup_percentage_fee', 'withdrawal_fixed_fee', 'withdrawal_percentage_fee', 'withdrawal_min_limit', 'withdrawal_max_limit', 'status'];

    public function cardProvider()
    {
        return $this->belongsTo(VirtualcardProvider::class, 'virtualcard_provider_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'virtualcard_currency_code', 'code');
    }
}
