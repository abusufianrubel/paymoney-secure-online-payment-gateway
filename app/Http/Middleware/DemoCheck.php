<?php

namespace App\Http\Middleware;

use App\Http\Helpers\Common;
use Closure;


class DemoCheck
{
    public function handle($request, Closure $next)
    {
        if (checkDemoEnvironment()) {
          Common::one_time_message('warning', __('Demo Mode! This action can\'t be performed'));
          return redirect()->back();
        }
        
        return $next($request);
    }
}
