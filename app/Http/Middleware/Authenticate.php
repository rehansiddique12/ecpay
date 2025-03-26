<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {

        // Check if the route prefix is 'admin'
        if (trim(request()->route()->getPrefix(), '/') == 'admin') {
            // Check if the user is not authenticated using the 'admin' guard
            if (!Auth::guard('admin')->check()) {
                return route('admin.login'); // Redirect to the admin login page
            }
        }

        // Check if the route prefix is 'partner'
        if (trim(request()->route()->getPrefix(), '/') == 'partner') {
            // Check if the user is not authenticated using the 'partner' guard
            if (!Auth::guard('partner')->check()) {
                return route('partner.login'); // Redirect to the partner login page
            }
        }

        // Default fallback (for other routes, such as regular user routes)
        if (!Auth::check()) {
            return route('login'); // Redirect to the regular login page
        }
    }
}
