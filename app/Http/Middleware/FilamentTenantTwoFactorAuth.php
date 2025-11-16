<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class FilamentTenantTwoFactorAuth
{
    /**
     * Handle an incoming request for Tenant panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::user();

        // Skip 2FA check if:
        // 1. User doesn't have 2FA enabled
        // 2. Already on 2FA challenge page
        // 3. Already verified this session
        if (!$user->hasEnabledTwoFactorAuthentication() 
            || $request->routeIs('filament.tenant.pages.two-factor-challenge')
            || session('auth.two_factor_confirmed_at')) {
            return $next($request);
        }

        // 🔥 LOG 2FA challenge redirect
        Log::info('2FA challenge required for tenant', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        // Redirect to Tenant 2FA challenge
        return redirect()->route('filament.tenant.pages.two-factor-challenge');
    }
}