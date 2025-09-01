<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if no user or user not authenticated yet
        if (!$user) {
            return $next($request);
        }

        $currentPath = $request->path();

        // Skip 2FA check for login pages and challenge pages
        if (str_contains($currentPath, 'login') || 
            str_contains($currentPath, 'two-factor-challenge')) {
            return $next($request);
        }

        // Only check 2FA if user is fully authenticated and has 2FA enabled
        try {
            if (Features::enabled(Features::twoFactorAuthentication()) && 
                method_exists($user, 'hasEnabledTwoFactorAuthentication') &&
                $user->hasEnabledTwoFactorAuthentication()) {
                
                // Check if 2FA was confirmed in this session
                if (!$request->session()->get('auth.two_factor_confirmed_at')) {
                    
                    // Determine which panel to redirect to
                    if (str_starts_with($currentPath, 'admin')) {
                        return redirect('/admin/two-factor-challenge');
                    } elseif (str_starts_with($currentPath, 'tenant')) {
                        return redirect('/tenant/two-factor-challenge');
                    }
                }
            }
        } catch (\Exception $e) {
            // If there's any error with 2FA checking, log it and continue
            Log::error('2FA Middleware Error: ' . $e->getMessage());
            return $next($request);
        }

        return $next($request);
    }
}