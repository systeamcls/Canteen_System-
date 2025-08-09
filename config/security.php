<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Headers Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for security headers middleware.
    | These headers help protect against various attacks and vulnerabilities.
    |
    */

    'headers' => [
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),
        
        'content_security_policy' => [
            'enabled' => env('CONTENT_SECURITY_POLICY_ENABLED', true),
            'policy' => env('CONTENT_SECURITY_POLICY', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
                "font-src 'self' https://fonts.gstatic.com; " .
                "img-src 'self' data: https:; " .
                "connect-src 'self'; " .
                "frame-src 'none'; " .
                "object-src 'none'; " .
                "base-uri 'self'; " .
                "form-action 'self'"
            ),
        ],

        'strict_transport_security' => [
            'max_age' => env('HSTS_MAX_AGE', 31536000),
            'include_subdomains' => env('HSTS_INCLUDE_SUBDOMAINS', true),
            'preload' => env('HSTS_PRELOAD', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for authentication rate limiting middleware.
    |
    */

    'rate_limiting' => [
        'authentication' => [
            'attempts' => env('RATE_LIMIT_AUTH_ATTEMPTS', 10),
            'decay_seconds' => env('RATE_LIMIT_AUTH_DECAY', 300),
        ],
        
        'api' => [
            'attempts' => env('RATE_LIMIT_API_ATTEMPTS', 60),
            'decay_seconds' => env('RATE_LIMIT_API_DECAY', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    |
    | Security settings for authentication processes.
    |
    */

    'authentication' => [
        'password_reset_expire_minutes' => env('AUTH_PASSWORD_RESET_EXPIRE', 15),
        'password_timeout_seconds' => env('AUTH_PASSWORD_TIMEOUT', 1800),
        'login_throttle_attempts' => env('AUTH_LOGIN_THROTTLE_ATTEMPTS', 5),
        'login_throttle_decay_seconds' => env('AUTH_LOGIN_THROTTLE_DECAY', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTPS Enforcement
    |--------------------------------------------------------------------------
    |
    | Force HTTPS in production environments for secure communication.
    |
    */

    'https' => [
        'enforce' => env('HTTPS_ENFORCE', app()->isProduction()),
        'trust_proxies' => env('TRUST_PROXIES', false),
    ],

];