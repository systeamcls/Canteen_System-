<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkusertype' => \App\Http\Middleware\CheckUserType::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'rate.limit.auth' => \App\Http\Middleware\RateLimitAuth::class,
            'ensure-2fa-admin' => \App\Http\Middleware\EnsureTwoFactorAuthenticatedForAdmin::class,
            'check-admin-cashier' => \App\Http\Middleware\CheckAdminOrCashierAccess::class,
            'filament.2fa' => \App\Http\Middleware\EnsureTwoFactorAuthenticated::class,
            'webhook.security' => \App\Http\Middleware\WebhookSecurityMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'redirect.admin' => \App\Http\Middleware\RedirectIfAdmin::class,

        ]);

        // Security headers globally to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Rate limiting to web routes
        $middleware->appendToGroup('web', \App\Http\Middleware\RateLimitAuth::class);

        // Raate limiting to API routes as wel
        $middleware->appendToGroup('api', \App\Http\Middleware\RateLimitAuth::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();