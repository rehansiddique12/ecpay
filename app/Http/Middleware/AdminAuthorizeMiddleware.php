<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthorizeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        $user = Auth::guard('admin')->user();
        $list = collect(config('role'))->pluck(['access'])->flatten();
        $filtered = $list->intersect($user->admin_access);

        if(!in_array($request->route()->getName(), $list->toArray()) ||  in_array($request->route()->getName(), $filtered->toArray()) ){
            return $next($request);
        }

        return  redirect()->route('admin.403');
    }
}
