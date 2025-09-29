<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CartService;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // Force HTTPS in production
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
        URL::forceRootUrl(config('app.url'));
        
        // Trust proxy headers for signed URLs
        $this->app['request']->server->set('HTTPS', 'on');
    }
}

}