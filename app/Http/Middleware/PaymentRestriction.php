<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentRestriction
{
    /**
     * Handle an incoming request.
     * Restrict payment methods based on user authentication status.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add payment restrictions to the request for use in components
        if (auth()->check()) {
            // Authenticated users can use both online and onsite payments
            $request->merge(['allowed_payment_methods' => ['online', 'onsite']]);
        } else {
            // Guest users can only use online payments
            $request->merge(['allowed_payment_methods' => ['online']]);
        }

        return $next($request);
    }
}
