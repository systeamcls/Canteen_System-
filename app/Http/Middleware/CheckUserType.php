<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $userType = null): Response
    {
        $sessionUserType = session('user_type');

        // If no user type is set, redirect to welcome page
        if (!$sessionUserType) {
            return redirect('/')->with('showModal', true);
        }

        // If specific user type is required, check it
        if ($userType && $sessionUserType !== $userType) {
            abort(403, 'Access denied. This page is only available for ' . $userType . ' users.');
        }

        return $next($request);
    }
}