<?php

namespace Modules\Virtualcard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardSpendingControl extends Model
{
    use HasFactory;

    protected $fillable = ['virtualcard_id', 'amount', 'interval', 'spent'];
}
