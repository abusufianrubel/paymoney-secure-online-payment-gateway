<?php

namespace Modules\Virtualcard\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = ['virtualcard_id', 'user_id', 'requested_fund', 'percentage', 'percentage_fees', 'fixed_fees', 'fund_request_time', 'fund_release_time', 'fund_approval_status', 'cancellation_reason', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function virtualcard()
    {
        return $this->belongsTo(Virtualcard::class);
    }

    public function transaction()
    {
        return $this->hasOne(\App\Models\Transaction::class, 'transaction_reference_id', 'id')->where('transaction_type_id', Virtualcard_Withdrawal);
    }
}
