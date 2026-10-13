<?php

namespace Modules\Virtualcard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Addons\Entities\Addon;


class CheckAddonsStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $virtualcardAddons = collect(Addon::all())->filter(function ($addon) {
            return $addon->get('type') === 'virtualcard' && $addon->get('core') !== true && isActive($addon);
        });

        if ($virtualcardAddons->isNotEmpty()) {
            return $next($request);
        } else {

            if (Auth::guard($guard)->check()) {
                if ($guard == 'users') {
                    return redirect()->route('user.dashboard');
                }
                elseif ($guard == 'admin') {
                    return redirect()->route('dashboard');
                }
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
