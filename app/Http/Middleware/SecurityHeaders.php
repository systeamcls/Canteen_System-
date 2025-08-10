<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply security headers if enabled
        if (!config('security.headers_enabled', true)) {
            return $response;
        }

        // Basic security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - Disable dangerous browser features
        $permissionsPolicy = implode(', ', [
            'accelerometer=()',
            'camera=()',
            'geolocation=()',
            'gyroscope=()',
            'magnetometer=()',
            'microphone=()',
            'payment=()',
            'usb=()',
            'interest-cohort=()',
        ]);
        $response->headers->set('Permissions-Policy', $permissionsPolicy);

        // Content Security Policy
        if (config('security.csp_enabled', true)) {
            $cspParts = config('security.csp', []);
            $csp = collect($cspParts)->map(function ($value, $directive) {
                return "{$directive} {$value}";
            })->implode('; ');

            $response->headers->set('Content-Security-Policy', $csp);
        }

        // HTTPS enforcement headers - Only in production and when not on localhost
        if (app()->environment('production') || config('security.https_enforce', false)) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // CSP header if enabled
        if (config('security.csp.enabled', false)) {
        $response->headers->set('Content-Security-Policy', config('security.csp.policy'));
    } 
            
        }

        return $response;
    }

    /**
     * Check if the request is coming from localhost
     */
    protected function isLocalhost(Request $request): bool
    {
        $host = $request->getHost();
        return in_array($host, ['localhost', '127.0.0.1', '::1']);
    }
}