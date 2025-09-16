<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Headers Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for security-related middleware
    | including security headers, rate limiting, and authentication settings.
    |
    */

    // Security Headers
    'headers_enabled' => env('SECURITY_HEADERS_ENABLED', true),
    'csp_enabled' => env('CONTENT_SECURITY_POLICY_ENABLED', true),
    'https_enforce' => env('HTTPS_ENFORCE', false),

    // Rate Limiting for Authentication Routes
    'rate_limit_auth_attempts' => env('RATE_LIMIT_AUTH_ATTEMPTS', 10), // Must be 10
    'rate_limit_auth_decay' => env('RATE_LIMIT_AUTH_DECAY', 300), // seconds

    // Authentication Security
    'password_reset_token_lifetime' => env('AUTH_PASSWORD_RESET_TOKEN_LIFETIME', 15), // minutes
    'password_confirmation_timeout' => env('AUTH_PASSWORD_CONFIRMATION_TIMEOUT', 1800), // seconds

    // API Token Security
    'sanctum_token_lifetime' => env('SANCTUM_TOKEN_LIFETIME', 60), // minutes

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP) Settings
    |--------------------------------------------------------------------------
    */
    'csp' => env('APP_ENV') === 'production'
    ? [   // Production rules
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com",
        'style-src'  => "'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
        'font-src'   => "'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
        'img-src'    => "'self' data: blob: https: *.gravatar.com ui-avatars.com",
        'connect-src'=> "'self'",
        'worker-src' => "'self' blob:",
        'frame-src'  => "'none'",
        'object-src' => "'none'",
        'base-uri'   => "'self'",
        'form-action'=> "'self'",
    ]
    : [   // Dev rules (with Vite + localhost + Filament requirements)
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173 ws://localhost:5173",
        'style-src'  => "'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173",
        'font-src'   => "'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
        'img-src'    => "'self' data: blob: http://localhost:8000 http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173 https: *.gravatar.com ui-avatars.com",
        'connect-src'=> "'self' ws://localhost:5173 ws://127.0.0.1:5173 ws://[::1]:5173 http://localhost:8000 http://localhost:5173",
        'worker-src' => "'self' blob:",
        'frame-src'  => "'none'",
        'object-src' => "'none'",
        'base-uri'   => "'self'",
        'form-action'=> "'self'",
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security Settings
    |--------------------------------------------------------------------------
    */
    'session' => [
        'encrypt' => env('SESSION_ENCRYPT', true),
        'secure_cookies' => env('SESSION_SECURE_COOKIES', 'auto'),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Security Settings
    |--------------------------------------------------------------------------
    */
    'redis' => [
        'tls_enabled' => env('REDIS_TLS_ENABLED', false),
        'tls_verify_peer' => env('REDIS_TLS_VERIFY_PEER', true),
        'tls_verify_peer_name' => env('REDIS_TLS_VERIFY_PEER_NAME', true),
    ],
];