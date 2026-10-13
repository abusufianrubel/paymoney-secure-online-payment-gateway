<?php

namespace Modules\Virtualcard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CardCreator
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (handleVirtualcardAccess()) {
            return $next($request);
        } else {
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}
