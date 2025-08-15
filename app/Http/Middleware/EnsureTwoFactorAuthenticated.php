<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated and is an admin
        if ($user && $user->hasRole('admin')) {
            // If two-factor authentication is not enabled, redirect to enable it
            if (!$user->two_factor_secret) {
                // Skip for two-factor related routes to avoid infinite redirects
                if (!$request->routeIs(['two-factor.*', 'profile.*'])) {
                    return redirect()->route('profile.show')
                        ->with('error', 'Two-factor authentication is required for admin users.');
                }
            }
        }

        return $next($request);
    }
}