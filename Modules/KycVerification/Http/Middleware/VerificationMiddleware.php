<?php

namespace Modules\KycVerification\Http\Middleware;

use Closure;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\DocumentVerification;

class VerificationMiddleware
{
    /**
     * Check if the kyc verification is mandatory.
     * If mandatory then check if the user is verified.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (settings('kyc_mandatory') !== 'Yes') {
            return $next($request);
        }

        $verification = DocumentVerification::getVerification();

        if (empty($verification) && (
            settings('kyc_required_for') === 'All' ||
            settings('kyc_required_for') == auth()->user()->role_id)
        ) {
            (new Common())->one_time_message('error', __('Please confirm your kyc verification.'));
            return redirect()->route('user.kyc.verifications.initiate');
        }

        return $next($request);
    }
}
