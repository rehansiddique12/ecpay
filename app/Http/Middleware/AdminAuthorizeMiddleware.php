<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TwoStepVerification;
use Illuminate\Support\Facades\Auth;

class AdminAuthorizeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        ////


        $user = Auth::guard('admin')->user();
        $list = collect(config('role'))->pluck(['access'])->flatten();
        $filtered = $list->intersect($user->admin_access);

        // if ($user->id == 1) {
        //     return $next($request);
        // }

        if(!in_array($request->route()->getName(), $list->toArray()) ||  in_array($request->route()->getName(), $filtered->toArray()) ){

            $TwoStepVerification = TwoStepVerification::where('user_id', $user->id)->where('type', 'Admin')
                ->first();
            if($TwoStepVerification){
                if($TwoStepVerification->g_auth_status=="Yes"){
                    return $next($request);
                }
            }
            if($request->path()=="admin/twoFA" || $request->path()=="admin/logout"){
               return $next($request);
            }else{
               
               return  redirect()->route('admin.twoFA');
            }


            // return $next($request);
        }

        if($request->route()->getName()=="admin.dashboard"){
            return  redirect()->route('admin.profile'); 
         }

        return  redirect()->route('admin.403');
    }
}
