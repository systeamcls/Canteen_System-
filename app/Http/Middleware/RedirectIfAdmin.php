<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            
            // Check if user is admin using Spatie
            if ($user->isAdmin()) {
                
                // Check if 2FA is enabled
                if ($user->hasEnabledTwoFactorAuthentication()) {
                    
                    // Check if already verified this session
                    if (!session('auth.two_factor_confirmed_at')) {
                        return redirect('/admin/two-factor-challenge');
                    }
                }
                
                // Redirect to admin dashboard
                return redirect('/admin');
            }
        }
        
        return $next($request);
    }
}