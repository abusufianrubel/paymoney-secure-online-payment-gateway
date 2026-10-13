<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Locale
{
    /**
     * Set the application locale from session or installer defaults (non-admin routes only).
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->isAdminRoute($request)) {
            return $next($request);
        }

        $this->seedDefaultLanguageSession();
        $this->applyLocale();

        return $next($request);
    }

    private function isAdminRoute(Request $request): bool
    {
        $route = $request->route();
        if ($route === null) {
            return false;
        }

        $prefix = str_replace('/', '', (string) $route->getPrefix());

        return $prefix === (string) config('adminPrefix');
    }

    private function appIsInstalled(): bool
    {
        return filter_var(env('APP_INSTALL'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * When the app is installed and no language is stored yet, persist the default language key in session.
     */
    private function seedDefaultLanguageSession(): void
    {
        if (! $this->appIsInstalled() || session()->has('dflt_lang')) {
            return;
        }

        $language = getLanguageDefault();
        if (! empty($language?->short_name)) {
            session(['dflt_lang' => $language->short_name]);
        }
    }

    /**
     * Prefer session language; otherwise load default language once the app is installed.
     */
    private function applyLocale(): void
    {
        if ($locale = session('dflt_lang')) {
            App::setLocale($locale);

            return;
        }

        if (! $this->appIsInstalled()) {
            return;
        }

        $language = getLanguageDefault();
        if (empty($language?->short_name)) {
            return;
        }

        App::setLocale($language->short_name);
        session(['dflt_lang' => $language->short_name]);
    }
}
