<?php

namespace App\Models;

use App\Models\{File, User};
use Illuminate\Database\Eloquent\Model;
use Modules\KycVerification\Entities\KycProvider;

class DocumentVerification extends Model
{
    protected $table = 'document_verifications';

    protected $fillable = [
        'user_id',
        'file_id',
        'verification_type',
        'identity_type',
        'identity_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function provider()
    {
        return $this->belongsTo(KycProvider::class, 'provider_id');
    }

    public function getVerifications()
    {
        $startForm = request()->startfrom ?? request()->from;
        $endTo = request()->endto ?? request()->to;

        $status = isset(request()->status) ? request()->status : 'all';
        $from = ! empty($startForm) ? setDateForDb($startForm) : null;
        $to = ! empty($endTo) ? setDateForDb($endTo) : null;
        $type = isset(request()->type) ? request()->type : 'all';
        $provider = isset(request()->provider) ? request()->provider : 'all';

        $conditions = [];

        $dateRange = empty($from) || empty($to) ? null : 'Available';

        if (!empty($status) && $status != 'all') {
            $conditions['status'] = $status;
        }

        if (!empty($type) && $type != 'all') {
            $conditions['verification_type'] = $type;
        }

        if (!empty($provider) && $provider != 'all') {
            $conditions['provider_id'] = $provider;
        }

        $verifications = $this->with([
            'user:id,first_name,last_name',
            'provider:id,name',
        ])->where($conditions);

        if (!empty($dateRange)) {
            $verifications->where(function ($query) use ($from, $to) {
                $query->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            });
        }

        return $verifications->select('document_verifications.id', 'document_verifications.user_id', 'document_verifications.provider_id', 'document_verifications.verification_type','document_verifications.status', 'document_verifications.created_at');
    }

    /**
     * Get user verification based on current kyc provider
     * @return Modules\KycVerification\Entities\KycUserVerification|null
     */

    public static function getVerification()
    {
        // active kyc provider
        $kycProvider = settings('kyc_provider');

        // active kyc provider id
        $provider = KycProvider::where('alias', $kycProvider)->first(['id']);

        // condition array
        $conditions = ['user_id' => auth()->id(), 'status' => 'approved'];

        // verification conditions based on kyc provider
        $conditions = ($kycProvider == 'manual')
                        ? $conditions + ['verification_type' => 'identity']
                        : $conditions + ['provider_id' => $provider->id];

        // get verification
        $verification = self::where($conditions)->first();

        // If the kyc provider is manual and identity verification is not empty
        // check for the address verification and return it

        if ($kycProvider === 'manual' && !empty($verification)) {
           return self::where(array_merge($conditions, ['verification_type' => 'address']))->first();
        }

        return $verification;
    }
}
