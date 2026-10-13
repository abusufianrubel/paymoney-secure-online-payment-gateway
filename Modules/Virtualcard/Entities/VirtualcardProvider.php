<?php

namespace Modules\Virtualcard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardProvider extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'currency_id', 'status'];

    public function feesLimit()
    {
        return $this->hasMany(VirtualcardFeeslimit::class);
    }

}
