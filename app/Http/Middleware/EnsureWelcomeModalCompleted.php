<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureWelcomeModalCompleted
{
    /**
     * Handle an incoming request.
     *
     * Restricts panel access based on user roles and permissions.
     * Prevents unauthorized access to /admin, /tenant, /cashier panels.
     * Includes security logging for unauthorized access attempts.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for non-panel routes
        if ($request->is('api/*') ||
            $request->is('logout') ||
            $request->is('livewire/*')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Security: Check if user is trying to access a panel they don't have permission for

        // Admin Panel Access Control
        if ($request->is('admin/*') || $request->is('admin')) {
            if (!$user->hasRole('admin') && !$user->hasRole('cashier')) {
                $this->logUnauthorizedAccess($user, 'admin', $request);
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                session()->flash('error', 'Unauthorized access attempt. You do not have permission to access the admin panel.');
                return redirect()->route('login');
            }
        }

        // Tenant Panel Access Control
        if ($request->is('tenant/*') || $request->is('tenant')) {
            if (!$user->hasRole('tenant') || !$user->is_active) {
                $this->logUnauthorizedAccess($user, 'tenant', $request);
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                session()->flash('error', 'Unauthorized access attempt. You do not have permission to access the tenant panel.');
                return redirect()->route('login');
            }
        }

        // Cashier Panel Access Control
        if ($request->is('cashier/*') || $request->is('cashier')) {
            if (!$user->hasRole('cashier') && !$user->hasRole('admin')) {
                $this->logUnauthorizedAccess($user, 'cashier', $request);
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                session()->flash('error', 'Unauthorized access attempt. You do not have permission to access the cashier panel.');
                return redirect()->route('login');
            }
        }

        return $next($request);
    }

    /**
     * Log unauthorized panel access attempts for security monitoring.
     *
     * @param  \App\Models\User  $user
     * @param  string  $panel
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function logUnauthorizedAccess($user, string $panel, Request $request): void
    {
        Log::warning('Unauthorized panel access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'attempted_panel' => $panel,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
