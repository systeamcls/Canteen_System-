<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticatedForAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if no user is authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip if route is already the 2FA challenge or logout
        if ($request->routeIs('two-factor.login') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Check if user requires 2FA and hasn't been verified in this session
        if ($user->requiresTwoFactor() && !session('auth.two_factor_confirmed')) {
            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}