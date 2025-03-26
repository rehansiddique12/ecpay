<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\PartnerLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleAuthenticatorService;
use App\Models\Api;
use App\Models\TwoStepVerification;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $googleAuthenticatorService;

    // use AuthenticatesUsers;
    protected $redirectTo = '/partner/dashboard';

    public function __construct(GoogleAuthenticatorService $googleAuthenticatorService)
    {
        $this->googleAuthenticatorService = $googleAuthenticatorService;
        // $this->middleware('guest:partner')->except('logout');
    }

    public function showLoginForm()
    {
        $data['title'] = "Partner Login";
        return view('partner.auth.login', $data);
    }


    public function login(Request $request)
    {
        $input = $request->all();


        $data['title'] = "Partner Login";

        $this->validate($request, [
            $this->username() => 'required',
            'password' => 'required',
        ]);

        $data['username'] = $request->username;
        $data['password'] = $request->password;

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $partner = Api::where($fieldType, $input['username'])->first();
        if ($partner && Hash::check($input['password'], $partner->password)) {

            $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)
                ->first();

            if($TwoStepVerification){
                if($TwoStepVerification->g_auth_status=="Yes"){
                    if(isset($request->otp)){
                        $checkResult = $this->googleAuthenticatorService->verifyCode($TwoStepVerification->g_secret_key, $request->otp, 0);
                        if($checkResult){
                            if(Auth::guard('partner')->attempt(array($fieldType => $input['username'], 'password' => $input['password']))){

                                $ipAddress = $_SERVER['REMOTE_ADDR'];
                                $user = Auth::guard('partner')->user();

                                $partnerlog = new PartnerLog();
                                $partnerlog->api_id = $user->id;
                                $partnerlog->log = "Login Successfully";
                                $partnerlog->ip_address = $ipAddress;
                                $partnerlog->save();


                                $thirtyDaysAgo = Carbon::now()->subDays(30);
                                PartnerLog::where('created_at', '<', $thirtyDaysAgo)->delete();



                                return redirect()->intended(route('partner.dashboard'));
                            }else{
                                return redirect()->route('partner.login')
                                    ->with('error','Email-Address And Password Are Wrong.');
                            }
                        }
                        $data['wrong'] = 'wrong';
                        return view('partner.auth.2fa', compact('data'));
                    }
                    return view('partner.auth.2fa', compact('data'));
                }
            }

        }

        if(Auth::guard('partner')->attempt(array($fieldType => $input['username'], 'password' => $input['password']))){

                                $ipAddress = $_SERVER['REMOTE_ADDR'];
                                $user = Auth::guard('partner')->user();

                                $partnerlog = new PartnerLog();
                                $partnerlog->api_id = $user->id;
                                $partnerlog->log = "Login Successfully";
                                $partnerlog->ip_address = $ipAddress;
                                $partnerlog->save();


            return redirect()->intended(route('partner.dashboard'));
        }else{
            return redirect()->route('partner.login')
                ->with('error','Email-Address And Password Are Wrong.');
        }

    }

    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        // $request->sessiodn()->invalidate();
        return $this->loggedOut($request) ?: redirect()->route('partner.login');
    }
    protected function loggedOut(Request $request)
    {
        return redirect()->route('partner.login');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, Auth::guard('partner')->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }



    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if($user->status == 0){
            Auth::guard('partner')->logout();
            return redirect()->route('partner.login')->with('error', 'You are banned from this application. Please contact with system Administrator.');
        }
        $user->last_login = Carbon::now();
        $user->save();


        $list = collect(config('role'))->pluck(['access','view'])->collapse()->intersect($user->admin_access);
        if(count($list) == 0){
            $list = collect(['partner.profile']);
        }

        return redirect()->intended(route($list->first()));

    }




}
