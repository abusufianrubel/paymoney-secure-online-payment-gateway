<?php

namespace Modules\Virtualcard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Virtualcard\Database\factories\VirtualcardSpendingFactory;

class VirtualcardSpending extends Model
{
    use HasFactory;

    protected $fillable = ['virtualcard_id', 'amount', 'category', 'transaction_date']; 
}
