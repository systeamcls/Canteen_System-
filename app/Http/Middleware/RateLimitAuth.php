<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply rate limiting to authentication-related routes
        if (!$this->isAuthRoute($request)) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request);
        $maxAttempts = env('RATE_LIMIT_AUTH_ATTEMPTS', 10);
        $decayMinutes = env('RATE_LIMIT_AUTH_DECAY', 300) / 60; // Convert seconds to minutes

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Too many authentication attempts. Please try again later.',
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::increment($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $maxAttempts - RateLimiter::attempts($key)),
        ]);

        return $response;
    }

    /**
     * Determine if the request is for an authentication route.
     */
    protected function isAuthRoute(Request $request): bool
    {
        $authPaths = [
            'login',
            'register',
            'password/email',
            'password/reset',
            'password/confirm',
            'two-factor-challenge',
            'user/profile-information',
            'user/password',
        ];

        $path = $request->path();
        
        foreach ($authPaths as $authPath) {
            if (str_contains($path, $authPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        // Use IP address and path for rate limiting key
        return sha1(
            $request->ip() . '|' . 
            $request->path() . '|' . 
            $request->method()
        );
    }
}