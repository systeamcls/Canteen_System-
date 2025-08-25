<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateways\PayMongoGateway;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the PaymentGateway interface to PayMongo implementation
        $this->app->bind(PaymentGatewayInterface::class, PayMongoGateway::class);

        // Register PaymentService as singleton
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(PaymentGatewayInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // You can add any bootstrapping logic here
    }
}