<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureWelcomeModalCompleted
{
    /**
     * Handle an incoming request.
     *
     * Ensures user completes welcome modal before accessing Filament panels.
     * Prevents direct URL access to /admin, /tenant, /cashier without modal completion.
     * Also validates user has permission to access the requested panel.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for API routes, logout, and welcome modal itself
        if ($request->is('api/*') ||
            $request->is('logout') ||
            $request->is('welcome-modal') ||
            $request->is('welcome-modal/*') ||
            $request->is('livewire/*')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Additional security: Check if user is trying to access a panel they don't have permission for
        if ($request->is('admin/*') || $request->is('admin')) {
            if (!$user->hasRole('admin') && !$user->hasRole('cashier')) {
                Auth::logout();
                session()->flash('error', 'You do not have permission to access the admin panel.');
                return redirect()->route('login');
            }
        }

        if ($request->is('tenant/*') || $request->is('tenant')) {
            if (!$user->hasRole('tenant') || !$user->is_active) {
                Auth::logout();
                session()->flash('error', 'You do not have permission to access the tenant panel.');
                return redirect()->route('login');
            }
        }

        if ($request->is('cashier/*') || $request->is('cashier')) {
            if (!$user->hasRole('cashier') && !$user->hasRole('admin')) {
                Auth::logout();
                session()->flash('error', 'You do not have permission to access the cashier panel.');
                return redirect()->route('login');
            }
        }

        // Check if user has completed the welcome modal this session
        if (!session()->has('welcome_modal_completed')) {
            // Store intended URL to redirect back after modal completion
            session()->put('intended_panel_url', $request->fullUrl());

            // Redirect to welcome modal
            return redirect()->route('welcome.modal');
        }

        return $next($request);
    }
}
