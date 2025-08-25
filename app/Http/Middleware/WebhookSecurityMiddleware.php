<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookSecurityMiddleware
{
    /**
     * Handle an incoming request for webhook endpoints
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log webhook attempts for security monitoring
        Log::info('Webhook request received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
        ]);

        // Check if request is from allowed IPs (optional)
        if (!$this->isAllowedIp($request->ip())) {
            Log::warning('Webhook request from disallowed IP', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Forbidden'], 403);
        }

        // Ensure it's a POST request
        if (!$request->isMethod('POST')) {
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        // Check for required headers
        if (!$request->header('content-type') || !str_contains($request->header('content-type'), 'application/json')) {
            return response()->json(['error' => 'Invalid content type'], 400);
        }

        return $next($request);
    }

    /**
     * Check if IP is allowed to access webhooks
     */
    private function isAllowedIp(string $ip): bool
    {
        // For development, allow all IPs
        if (app()->environment('local')) {
            return true;
        }

        // PayMongo webhook IP ranges (update based on their documentation)
        $allowedIps = [
            '127.0.0.1',
            '::1',
            // Add PayMongo's actual webhook IP ranges here
        ];

        // Allow localhost
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return true;
        }

        return in_array($ip, $allowedIps);
    }
}