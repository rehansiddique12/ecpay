<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\IpWhitelist;

class CheckAdminStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $admin = Admin::find(Auth::guard('admin')->id()); // fresh DB call

            // 1. Check if account is active
            if ($admin && $admin->status == 0) {
                return $this->forceLogout($request, 'Your account is inactive. Please contact administrator.');
            }

            // 2. Check if IP is allowed
            $ipAddress = $request->ip();
            if ($ipAddress === '::1') {
                $ipAddress = '127.0.0.1';
            }
            $allowed = IpWhitelist::where('ip_address', $ipAddress)->exists();

            if (!$allowed) {
                return $this->forceLogout($request, 'You have no permission to access from this IP: ' . $ipAddress);
            }
        }

        return $next($request);
    }

    private function forceLogout(Request $request, string $message)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('error', $message);
    }
}
