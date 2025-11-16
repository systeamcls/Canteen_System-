<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsurePanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 🔥 SECURITY: Enforce HTTPS in production
        if (!$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }
        
        // If not authenticated, redirect to welcome page with modal open
        if (!Auth::check()) {
            session()->flash('showModal', true);
            session()->flash('redirectIntended', $request->url());
            session()->flash('loginMessage', 'Please log in to access this area.');
            
            Log::info('Unauthenticated panel access attempt blocked', [
                'required_role' => $role,
                'requested_url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
            
            return redirect()->route('welcome');
        }

        // Check if user has the required role
        $user = Auth::user();
        
        if (!$user->hasRole($role)) {
            // 🔥 SECURITY: Log unauthorized access attempt
            Log::warning('Unauthorized panel access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'required_role' => $role,
                'user_roles' => $user->roles->pluck('name')->toArray(),
                'requested_url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
            
            abort(403, 'Unauthorized access to this panel. You do not have the required role.');
        }

        return $next($request);
    }
}