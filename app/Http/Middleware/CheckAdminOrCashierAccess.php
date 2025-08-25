<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;


class CheckAdminOrCashierAccess
{   
    
    public function handle(Request $request, Closure $next): Response
    {   
         /** @var \App\Models\User|\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        // CRITICAL: Only allow admin or cashier roles
        if (!$user->hasAnyRole(['admin', 'cashier'])) {
            // Log security violation
            Log::warning('Unauthorized admin panel access attempt', [
                'user_id'    => $user->id,
                'user_email' => $user->email,
                'user_roles' => $user->getRoleNames(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Force logout and redirect
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            
            return redirect('/login')->with('error', 'Access denied. Admin or Cashier access required.');
        }

        return $next($request);
    }
}
