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
        ]);

        // Apply security headers globally to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Apply authentication rate limiting to web routes
        $middleware->appendToGroup('web', \App\Http\Middleware\RateLimitAuth::class);

        // Apply authentication rate limiting to API routes as well
        $middleware->appendToGroup('api', \App\Http\Middleware\RateLimitAuth::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();