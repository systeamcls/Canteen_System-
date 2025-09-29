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
    // TEMPORARILY DISABLED FOR SETUP
    return $next($request);
    
    // ... rest of code commented out
}
}
