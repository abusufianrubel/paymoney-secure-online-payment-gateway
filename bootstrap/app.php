<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $adminPrefix = trim((string) env('ADMIN_PREFIX', 'admin'), '/');

        $middleware->preventRequestsDuringMaintenance(except: [
            $adminPrefix.'/system-update',
            $adminPrefix.'/system-update/*',
            '*/system-update',
            '*/system-update/*',
        ]);

        $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'no_auth' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'guest' => \App\Http\Middleware\Guest::class,
        'ip_middleware' => \App\Http\Middleware\IpMiddleware::class,
        'locale' => \App\Http\Middleware\Locale::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'twoFa' => \App\Http\Middleware\twoFa::class,
        'google2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
        'check-user-suspended' => \App\Http\Middleware\CheckUserSuspended::class,
        'check-user-inactive' => \App\Http\Middleware\CheckUserInactive::class,
        'check-authorization-token' => \App\Http\Middleware\CheckAuthorizationToken::class,
        'api_version' => \App\Http\Middleware\APIversion::class,
        'demo_check' => \App\Http\Middleware\DemoCheck::class,
        ]);

        // Default "web" group already includes session + CSRF + bindings. Replace
        // framework classes with app subclasses so $except in App\VerifyCsrfToken applies.
        // Do not append a second CSRF middleware — the first (framework) would still run and ignore your exclusions.
        $middleware->web(
            append: [
                \App\Http\Middleware\Locale::class,
            ],
            replace: [
                \Illuminate\Cookie\Middleware\EncryptCookies::class => \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
            ]
        );

        $middleware->api(append: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
