<?php

namespace Modules\Virtualcard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Virtualcard extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    public function virtualcardProvider()
    {
        return $this->belongsTo(VirtualcardProvider::class);
    }

    public function virtualcardCategory()
    {
        return $this->belongsTo(VirtualcardCategory::class);
    }
   
    public function virtualcardHolder()
    {
        return $this->belongsTo(VirtualcardHolder::class, 'virtualcard_holder_id', 'id');
    }

    public function currency()
    {
        return \App\Models\Currency::where('code', $this->currency_code)->first();
    }

    public function virtualcardWithdrawals()
    {
        return $this->hasMany(VirtualcardWithdrawal::class);
    }

    public function spendingControls()
    {
        return $this->hasMany(VirtualcardSpendingControl::class);
    }
}
