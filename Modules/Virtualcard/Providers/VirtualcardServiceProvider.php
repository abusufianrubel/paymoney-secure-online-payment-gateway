<?php

namespace Modules\Virtualcard\Providers;

use Modules\Virtualcard\Http\Middleware\{
    CheckKYC,
    CardCreator,
    CheckAddonsStatus
};
use App\Models\{
    User, 
    Transaction
};
use Modules\Virtualcard\Entities\{
    VirtualcardTopup,
    VirtualcardHolder,
    VirtualcardWithdrawal
};
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class VirtualcardServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Virtualcard';

    protected string $moduleNameLower = 'virtualcard';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        # Register Middleware
        $this->app['router']->aliasMiddleware('check.kyc', CheckKYC::class);
        $this->app['router']->aliasMiddleware('card.creator', CardCreator::class);
        $this->app['router']->aliasMiddleware('addon.status', CheckAddonsStatus::class);

        # Dynamic Relation of load from User
        User::resolveRelationUsing('virtualcardWithdrawals', function (User $userModel) {
            return $userModel->hasMany(VirtualcardWithdrawal::class, 'user_id'); // 'user_id' is the foreign key
        });
        # Dynamic Relation of load from Transaction
        Transaction::resolveRelationUsing('virtualcardWithdrawal', function (Transaction $transactionModel) {
            return $transactionModel->belongsTo(VirtualcardWithdrawal::class, 'transaction_reference_id', 'id');
        });

        # Dynamic Relation of load from Transaction
        Transaction::resolveRelationUsing('virtualcardTopup', function (Transaction $transactionModel) {
            return $transactionModel->belongsTo(VirtualcardTopup::class, 'transaction_reference_id', 'id');
        });

        # register blade components
        Blade::componentNamespace('Modules\\Virtualcard\\View\\Components', 'virtualcard');

         # register helpers
         $helper = __DIR__ . '/../Helpers/helpers.php';
         if (file_exists($helper)) {
            require_once $helper;
         }

        # Dynamic Relation of load from User
        User::resolveRelationUsing('virtualcardHolders', function (User $userModel) {
            return $userModel->hasMany(VirtualcardHolder::class, 'user_id');
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace').'\\'.$this->moduleName.'\\'.config('modules.paths.generator.component-class.path'));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
