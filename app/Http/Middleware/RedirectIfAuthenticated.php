<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Send guests to the next handler; redirect authenticated users away from guest-only routes.
     */
    public function handle(Request $request, Closure $next, ?string $role = null): mixed
    {
        if (! is_string($role) || $role === '' || ! Auth::guard($role)->check()) {
            return $next($request);
        }

        return $this->redirectForGuard($role) ?? $next($request);
    }

    private function redirectForGuard(string $role): ?Response
    {
        return match ($role) {
            'users' => Redirect::to('dashboard'),
            'admin' => Redirect::route('dashboard'),
            'agent' => $this->agentDashboardRedirect(),
            default => null,
        };
    }

    /**
     * Only redirect agents when guard config uses provider name "agent" (legacy convention).
     */
    private function agentDashboardRedirect(): ?Response
    {
        $provider = config('auth.guards.agent.provider');

        if ($provider !== 'agent') {
            return null;
        }

        return Redirect::route('agent.dashboard');
    }
}
