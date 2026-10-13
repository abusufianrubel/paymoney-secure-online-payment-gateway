<?php

namespace Modules\Virtualcard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckKYC
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (preference('card_kyc') == 'Yes' && kycRequired()['status'] == \Illuminate\Http\Response::HTTP_BAD_REQUEST) {
            return redirect(kycRequired()['url']);
        }
        
        return $next($request);
    }
}
