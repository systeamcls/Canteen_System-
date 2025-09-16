<?php
// app/Http/Middleware/CheckCashierAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckCashierAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        // CRITICAL: Only allow cashier and admin roles for operational access
        if (!$user->hasAnyRole(['cashier', 'admin'])) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized cashier panel access attempt', [
                'user_id'    => $user->id,
                'user_email' => $user->email,
                'user_roles' => $user->getRoleNames(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'intended_url' => $request->url(),
            ]);

            // Force logout and redirect with error
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            
            return redirect('/login')->with('error', 'Access denied. Cashier access required.');
        }

        // Additional check: Ensure user is active
        if (!$user->is_active) {
            Log::warning('Inactive user attempted cashier access', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
            
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            
            return redirect('/login')->with('error', 'Your account is inactive. Please contact administrator.');
        }

        return $next($request);
    }
}