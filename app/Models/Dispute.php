<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $table    = 'disputes';
    protected $fillable = [
        'claimant_id',
        'defendant_id',
        'transaction_id',
        'reason_id',
        'title',
        'description',
        'code',
        'status',
    ];

    public function claimant()
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function defendant()
    {
        return $this->belongsTo(User::class, 'defendant_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id');
    }

    public function disputeDiscussions()
    {
        return $this->hasMany(DisputeDiscussion::class, 'dispute_id')->orderBy('id', 'desc');
    }

    public function getDisputesUsersName($user)
    {
        return $this->join('users', function ($join)
        {
            $join->on('users.id', '=', 'disputes.claimant_id')->orOn('users.id', '=', 'disputes.defendant_id');

        })
        ->where(['disputes.claimant_id' => $user])
        ->orWhere(['disputes.defendant_id' => $user])
        ->select('users.first_name', 'users.last_name', 'users.id', 'disputes.claimant_id', 'disputes.defendant_id')
        ->first();
    }

    public function getDisputesUsersResponse($search)
    {
        return $this->join('users', function ($join)
        {
            $join->on('users.id', '=', 'disputes.claimant_id')->orOn('users.id', '=', 'disputes.defendant_id');
        })
        ->where('users.first_name', 'LIKE', '%' . $search . '%')
        ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
        ->distinct('users.first_name')
        ->select('users.first_name', 'users.last_name', 'users.id')
        ->get();
    }

    public function getDisputesList($from, $to, $status, $user)
    {
        $date_range = 'Available';

        if (empty($from) || empty($to)){
            $date_range = null;
        }

        $disputes = $this->with([
            'claimant:id,first_name,last_name,picture',
            'defendant:id,first_name,last_name,picture'
        ])
        ->where(function ($query) use ($status, $user) {
            $query->where(function ($q) use ($status, $user) {
                if (!empty($status) && $status != 'all') {
                    $q->where('disputes.status', $status);
                }

                if (!empty($user)) {
                    $q->where('disputes.claimant_id', $user);
                }
            })
            ->orWhere(function ($q) use ($status, $user) {
                if (!empty($status) && $status != 'all') {
                    $q->where('disputes.status', $status);
                }

                if (!empty($user)) {
                    $q->where('disputes.defendant_id', $user);
                }
            });
        });

        if (!empty($date_range)) {
            $disputes->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
        }

        return $disputes->select('disputes.*');
    }

    public function latestDispute()
    {
        return $this->leftJoin('users', 'users.id', '=', 'disputes.claimant_id')
            ->leftJoin('reasons', 'reasons.id', '=', 'disputes.reason_id')
            ->where(['disputes.status' => 'Open'])
            ->select('disputes.*', 'users.first_name', 'users.last_name', 'reasons.title as reason_title')
            ->orderBy('disputes.id', 'DESC')
            ->take(5)
            ->get();
    }
}
