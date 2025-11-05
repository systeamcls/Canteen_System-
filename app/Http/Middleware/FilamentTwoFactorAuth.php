<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentTwoFactorAuth
{
    /**
     * Handle an incoming request.
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
            || $request->routeIs('filament.admin.pages.two-factor-challenge')
            || session('auth.two_factor_confirmed_at')) {
            return $next($request);
        }

        // Redirect to 2FA challenge
        return redirect()->route('filament.admin.pages.two-factor-challenge');
    }
}