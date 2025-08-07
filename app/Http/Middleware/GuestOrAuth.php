<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestOrAuth
{
    /**
     * Handle an incoming request.
     * Allow both guest and authenticated users to access the route.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always allow access - no authentication required
        // This middleware just tracks that the route allows guest access
        return $next($request);
    }
}
