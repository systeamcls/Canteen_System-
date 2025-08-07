<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\PaymentPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register payment gates
        Gate::define('use-onsite-payment', [PaymentPolicy::class, 'useOnsitePayment']);
        Gate::define('use-online-payment', [PaymentPolicy::class, 'useOnlinePayment']);
        Gate::define('browse-as-guest', [PaymentPolicy::class, 'browseAsGuest']);
        Gate::define('access-cashier', [PaymentPolicy::class, 'accessCashier']);
        Gate::define('manage-stalls', [PaymentPolicy::class, 'manageStalls']);
        Gate::define('view-reports', [PaymentPolicy::class, 'viewReports']);
    }
}
