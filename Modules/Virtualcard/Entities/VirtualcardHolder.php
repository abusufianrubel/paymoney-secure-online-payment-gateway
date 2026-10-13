<?php

namespace Modules\Virtualcard\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardHolder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function virtualcardProvider()
    {
        return $this->belongsTo(VirtualcardProvider::class);
    }
    
        
    /**
     * Get a list of virtual card holders with optional filters.
     *
     * This method retrieves virtual card holders based on various filter criteria, 
     * including status, country, holder type, user, and a date range.
     *
     * @param string|null $from       The start date for filtering (format: YYYY-MM-DD).
     * @param string|null $to         The end date for filtering (format: YYYY-MM-DD).
     * @param string      $status     The status of the cardholder ('all' for no filtering).
     * @param string      $country    The country of the cardholder ('all' for no filtering).
     * @param string      $holderType The type of cardholder ('all' for no filtering).
     * @param int|null    $user       The user ID to filter by (null for no filtering).
     *
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance with applied filters.
     */
    public function getCardHoldersList($from, $to, $status, $country, $holderType, $user)
    {
        $query = $this->with(['user:id,first_name,last_name'])->select('virtualcard_holders.id', 'virtualcard_holders.first_name', 'virtualcard_holders.last_name', 'virtualcard_holders.business_name', 'virtualcard_holders.user_id', 'virtualcard_holders.card_holder_type', 'virtualcard_holders.country', 'virtualcard_holders.status', 'virtualcard_holders.created_at');

        // Apply filters dynamically
        $filters = [
            'status'            => $status !== 'all' ? $status : null,
            'card_holder_type'  => $holderType !== 'all' ? $holderType : null,
            'country'           => $country !== 'all' ? $country : null,
            'user_id'           => !empty($user) ? $user : null,
        ];

        // Remove null values and apply conditions
        $query->where(array_filter($filters));

        // Handle date range filter correctly
        if (!empty($from) && !empty($to)) {
            if ($from === $to) {
                // If both dates are the same, fetch records for the entire day
                $query->whereDate('created_at', '=', $from);
            } else {
                // Normal date range filtering
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        return $query;
    }

    public function virtualcardHoldersUsersName($user)
    {
        return $this->leftJoin('users', 'users.id', '=', 'virtualcard_holders.user_id')
            ->where(['user_id' => $user])
            ->select('users.first_name', 'users.last_name', 'users.id')
            ->first();
    }

    public function getCardUsersResponse($search)
    {
        return $this->leftJoin('users', 'users.id', '=', 'virtualcard_holders.user_id')
            ->where('users.first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
            ->distinct('users.first_name')
            ->select('users.first_name', 'users.last_name', 'virtualcard_holders.user_id')
            ->get();
    }
}
