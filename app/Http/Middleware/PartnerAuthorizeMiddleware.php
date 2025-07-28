<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TwoStepVerification;
use Illuminate\Support\Facades\Session;

class PartnerAuthorizeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        $user = Auth::guard('partner')->user();
        $Adminuser = Auth::guard('admin')->user();
        if(isset($Adminuser->id)){
            $can_access_admin = adminAccessRoute(config('role.view_partner_account.access.view'));
            if ($can_access_admin) {
                return $next($request);
            }
        }

        $list = collect(config('rolep'))->pluck(['access'])->flatten();
        $filtered = $list->intersect($user->admin_access);


        $loginTimestamp = Session::get('login_timestamp_partner');
        if($user->last_session_id!=$loginTimestamp){
            Auth::guard('partner')->logout();
            Session::flush();
            return redirect()->route('partner.login')->with('error', 'You have been logged out because your account was accessed from another location.');
        }

        if(!in_array($request->route()->getName(), $list->toArray()) ||  in_array($request->route()->getName(), $filtered->toArray()) ){

            $TwoStepVerification = TwoStepVerification::where('user_id', $user->id)->where('type', 'Partner')
                ->first();
            if($TwoStepVerification){
                if($TwoStepVerification->g_auth_status=="Yes"){
                    return $next($request);
                }
            }
            if($request->path()=="partner/twoFA" || $request->path()=="partner/logout"){
               return $next($request);
            }else{

               return  redirect()->route('partner.twoFA');
            }
        }

        if($request->route()->getName()=="partner.dashboard"){
           return  redirect()->route('partner.profile');
        }

        return  redirect()->route('partner.403');
    }
}
