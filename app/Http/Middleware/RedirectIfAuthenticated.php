<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{


    public function handle(Request $request, Closure $next, ...$guards)
    {
        // If no guards are provided, set a default guard to null
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {
                // Use a switch or if-else based on guard types
                switch ($guard) {
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    case 'partner':
                        return redirect()->route('partner.dashboard');
                    default:
                        return redirect()->route('welcome');
                }
            }
        }

        return $next($request);
    }
}
