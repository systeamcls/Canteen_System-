<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Not logged in - redirect to welcome page
            return redirect()->route('welcome')
                ->with('error', 'Please login to access this page.');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                // User has the required role, allow access
                return $next($request);
            }
        }

        // User is logged in but doesn't have the required role
        return redirect()->route('home.index')
            ->with('error', 'You do not have permission to access this page.');
    }
}