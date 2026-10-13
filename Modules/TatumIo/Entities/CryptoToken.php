<?php

namespace Modules\TatumIo\Entities;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class CryptoToken extends Model
{
    protected $table    = 'crypto_tokens';

    protected $fillable = [
        'txId',
        'currency_id',
        'symbol',
        'address',
        'decimals',
        'payment_type',
        'name',
        'value',
        'address',
        'network'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }


}
