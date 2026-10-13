<?php

namespace App\Providers;

use App\libraries\VerificationProviderManager as VerificationProviderManagerConcrete;
use App\Models\PaymentMethod;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Virtualcard\Manager\ProviderManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! $this->app->bound('verificationprovidermanager')) {
            $this->app->singleton('verificationprovidermanager', function () {
                return new VerificationProviderManagerConcrete;
            });
        }

        if (! $this->app->bound('virtualcardprovidermanager')) {
            $this->app->singleton('virtualcardprovidermanager', function () {
                return new ProviderManager;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for database columns
        Schema::defaultStringLength(191);

        // Clear the X-Powered-By header for security reasons
        header('x-powered-by:');

        if (env('APP_INSTALL') == true) {
            $this->useArrayCacheWhenDatabaseCacheTableMissing();

            if (Schema::hasTable('transaction_types')) {
                $transactionTypes = TransactionType::all()->toArray();

                foreach ($transactionTypes as $transactionType) {
                    if (!defined($transactionType['name'])) {
                        define($transactionType['name'], $transactionType['id']);
                    }
                }
            }

            if (Schema::hasTable('payment_methods')) {
                $paymentMethods = PaymentMethod::all()->toArray();

                foreach ($paymentMethods as $paymentMethod) {
                    if (!defined($paymentMethod['name'])) {
                        define($paymentMethod['name'], $paymentMethod['id']);
                    }
                }
            }

            $adminUrlPrefix = preference('admin_url_prefix');
            if (!empty($adminUrlPrefix)) {
                Config::set(['adminPrefix' => $adminUrlPrefix]);
                View::share('adminPrefix', $adminUrlPrefix);
            }
        }
    }

    /**
     * v5+ uses database cache; during upgrade the cache table may not exist yet.
     * Fall back to array driver so preference() and other boot logic can run.
     */
    private function useArrayCacheWhenDatabaseCacheTableMissing(): void
    {
        if (config('cache.default') !== 'database') {
            return;
        }

        try {
            if (! Schema::hasTable('cache')) {
                Config::set('cache.default', 'array');

                if ($this->app->bound('cache')) {
                    $this->app->forgetInstance('cache');
                    $this->app->forgetInstance('cache.store');
                }
            }
        } catch (\Throwable $e) {
            Config::set('cache.default', 'array');
        }
    }
}
