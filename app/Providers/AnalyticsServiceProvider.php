<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AnalyticsService;
use App\Services\ReportService;

class AnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AnalyticsService::class);
        $this->app->singleton(ReportService::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/analytics.php' => config_path('analytics.php'),
        ], 'analytics-config');
    }
}