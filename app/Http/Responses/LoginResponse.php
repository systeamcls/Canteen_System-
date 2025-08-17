<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();
        
        // Determine redirect URL based on user role
        $redirectUrl = $this->getRedirectUrl($user);
        
        // Log the login for security tracking
        \Log::info('User login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'redirect_to' => $redirectUrl,
            'ip' => $request->ip(),
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : redirect()->intended($redirectUrl);
    }

    private function getRedirectUrl($user): string
    {
        // Admin and Cashier -> Admin Panel
        if ($user->hasRole('admin') || $user->hasRole('cashier')) {
            return '/admin';
        }
        
        // Tenant -> Tenant Panel
        if ($user->hasRole('tenant')) {
            return '/tenant';
        }
        
        // Customer -> Public Site (when you build it)
        if ($user->hasRole('customer')) {
            return '/dashboard'; // Customer dashboard
        }
        
        // Unknown role -> logout and redirect to login
        Auth::logout();
        return '/login';
    }
}