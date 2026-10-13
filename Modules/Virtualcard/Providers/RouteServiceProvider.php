<?php

namespace Modules\Virtualcard\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Virtualcard\Entities\VirtualcardHolder;
use Modules\Virtualcard\Entities\VirtualcardProvider;
use Modules\Virtualcard\Entities\VirtualcardWithdrawal;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Virtualcard\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();

        Route::model('virtualcardholder', VirtualcardHolder::class);
        Route::model('virtualcardprovider', VirtualcardProvider::class);

        Route::model('virtualcardwithdrawal', VirtualcardWithdrawal::class);

    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Virtualcard', '/Routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api/v2')
            ->middleware('api', 'api_version:v2')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Virtualcard', '/Routes/api.php'));
    }
}
