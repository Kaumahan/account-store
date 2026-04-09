<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Use (int) or == to avoid issues if the DB returns '1' as a string
        if (auth()->check() && (int) auth()->user()->is_admin === 1) {
            return $next($request);
        }

        abort(403, 'Access Denied: You are not an administrator.');
    }
}