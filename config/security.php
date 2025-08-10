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
    'csp' => [
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
        'style-src' => "'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
        'font-src' => "'self' https://fonts.gstatic.com",
        'img-src' => "'self' data: https:",
        'connect-src' => "'self'",
        'frame-src' => "'none'",
        'object-src' => "'none'",
        'base-uri' => "'self'",
        'form-action' => "'self'",
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