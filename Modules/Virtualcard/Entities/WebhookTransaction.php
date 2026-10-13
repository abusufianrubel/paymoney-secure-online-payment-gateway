<?php

namespace Modules\Virtualcard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookTransaction extends Model
{
    use HasFactory;

    protected $table   = 'virtualcard_transactions';

    protected $guarded = [];

    public function virtualcard()
    {
        return $this->belongsTo(Virtualcard::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

}
