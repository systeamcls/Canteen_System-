<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
{
    // Only check verification for authenticated employee users at checkout
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if (Auth::check() && 
        session('user_type') === 'employee' && 
        !$user->hasVerifiedEmail() &&
        $request->is('checkout*')) {
        return redirect()->route('verification.notice');
    }

    return $next($request);
}


}