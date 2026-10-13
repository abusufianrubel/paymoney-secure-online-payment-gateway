<?php

namespace Modules\Virtualcard\Entities;

use App\Models\{
    User,
    Transaction
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardTopup extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function virtualcard()
    {
        return $this->belongsTo(Virtualcard::class, 'virtualcard_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id', 'transaction_reference_id')->where('transaction_type_id', Virtualcard_Topup);
    }

    public function virtualcardTopupsUsersName($user)
    {
        return $this->leftJoin('users', 'users.id', '=', 'virtualcard_topups.user_id')
            ->where(['user_id' => $user])
            ->select('users.first_name', 'users.last_name', 'users.id')
            ->first();
    }

    public function getTopupUsersResponse($search)
    {
        return $this->leftJoin('users', 'users.id', '=', 'virtualcard_topups.user_id')
            ->where('users.first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
            ->distinct('users.first_name')
            ->select('users.first_name', 'users.last_name', 'virtualcard_topups.user_id')
            ->get();
    }

    public function getVirtualcardTopupsList($from, $to, $status, $currency, $brand, $user)
    {
        return $this->with([
                'user:id,first_name,last_name',
                'virtualcard:id,card_brand,card_number,currency_code',
            ])
            ->select('virtualcard_topups.*')
            ->when($status !== 'all', fn($query) => $query->where('fund_approval_status', $status))
            ->when(!empty($user), fn($query) => $query->where('user_id', $user))
            ->when($brand !== 'all' && !empty($brand), fn($query) =>
                $query->whereHas('virtualcard', fn($q) => $q->where('card_brand', $brand))
            )
            ->when($currency !== 'all' && !empty($currency), fn($query) =>
                $query->whereHas('virtualcard', fn($q) => $q->where('currency_code', $currency))
            )
            ->when(!empty($from) && !empty($to), fn($query) =>
                $from === $to
                    ? $query->whereDate('created_at', $from)
                    : $query->whereBetween('created_at', [$from, $to])
            );
    }

}
