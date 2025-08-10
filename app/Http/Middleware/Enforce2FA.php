<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Enforce2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has admin or tenant role and enforce 2FA
            if ($user->hasAnyRole(['admin', 'tenant'])) {
                // Check if 2FA is not enabled
                if (!$user->two_factor_confirmed_at) {
                    // Redirect to 2FA setup page
                    return redirect()->route('profile.show')
                        ->with('warning', 'Two-Factor Authentication is required for your account. Please set it up to continue.');
                }
            }
        }

        return $next($request);
    }
}