<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminHasStall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            $adminStall = Auth::user()->stall;
            
            if (!$adminStall) {
                // Redirect to profile or stall setup page if admin has no stall
                return redirect()->route('filament.admin.pages.profile')
                    ->with('warning', 'Please contact the system administrator to assign you a stall before accessing this feature.');
            }
        }

        return $next($request);
    }
}