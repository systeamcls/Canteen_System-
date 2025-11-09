<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized action. You do not have the required role.');
        }

        // Additional security: Check if account is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        return $next($request);
    }
}