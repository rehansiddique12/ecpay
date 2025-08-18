<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TwoStepVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    protected $googleAuthenticatorService;

    protected $redirectTo = '/admin/dashboard';

    public function __construct(GoogleAuthenticatorService $googleAuthenticatorService)
    {
        $this->googleAuthenticatorService = $googleAuthenticatorService;
    }

    // Show the login form
    public function showLoginForm()
    {
        $data['title'] = "Admin Login";
        return view('admin.auth.login', $data);
    }

    // Handle the login logic
    public function login(Request $request)
    {

        $input = $request->all();
        $this->validate($request, [
            $this->username() => 'required',
            'password' => 'required',
        ]);

        $data['username'] = $request->username;
        $data['password'] = $request->password;


        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $partner = Admin::where($fieldType, $input['username'])->first();
        if ($partner && Hash::check($input['password'], $partner->password)) {

            $timestamp = time();
            $partner->last_session_id = $timestamp;
            $partner->save();


            Session::put('login_timestamp', $timestamp);



            // if(Auth::guard('admin')->attempt(array($fieldType => $input['username'], 'password' => $input['password']))){



            //     $ipAddress = $_SERVER['REMOTE_ADDR'];
            //     $user = Auth::guard('admin')->user();


            //     return redirect()->intended(route('admin.dashboard'));
            // }else{
            //     return redirect()->route('admin.login')
            //         ->with('error','Email-Address And Password Are Wrong.');
            // }

            $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)->where('type', 'Admin')
                ->first();
            if($TwoStepVerification){
                if($TwoStepVerification->g_auth_status=="Yes"){
                    if(isset($request->otp)){
                        $checkResult = $this->googleAuthenticatorService->verifyCode($TwoStepVerification->g_secret_key, $request->otp, 0);
                        if($checkResult){
                            if(Auth::guard('admin')->attempt(array($fieldType => $input['username'], 'password' => $input['password']))){



                                $ipAddress = $_SERVER['REMOTE_ADDR'];
                                $user = Auth::guard('admin')->user();


                                return redirect()->intended(route('admin.dashboard'));
                            }else{
                                return redirect()->route('admin.login')
                                    ->with('error','Email-Address And Password Are Wrong.');
                            }
                        }
                        $data['wrong'] = 'wrong';
                        return view('admin.auth.2fa', compact('data'));
                    }
                    return view('admin.auth.2fa', compact('data'));
                }
            }

        }

        if(Auth::guard('admin')->attempt(array($fieldType => $input['username'], 'password' => $input['password']))){

                                $ipAddress = $_SERVER['REMOTE_ADDR'];
                                $user = Auth::guard('admin')->user();

            return redirect()->intended(route('admin.dashboard'));
        }else{
            return redirect()->route('admin.login')
                ->with('error','Email-Address And Password Are Wrong.');
        }


        // $input = $request->all();

        // // Validate the request
        // $this->validateLogin($request);

        // // Determine if the user is logging in with email or username
        // $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // // Attempt to log the user in
        // if (Auth::guard('admin')->attempt([$fieldType => $input['username'], 'password' => $input['password']])) {
        //     return $this->sendLoginResponse($request);
        // } else {
        //     return redirect()->route('admin.login')
        //         ->with('error', 'Email-Address or Username and Password are wrong.');
        // }
    }

    // Define the username field (either email or username)
    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    // Validate the login form input
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    // Define which authentication guard to use (for admins)

    // Handle logout and clear the session
    // public function logout(Request $request)
    // {
    //     // $this->guard()->logout();
    //     Auth::guard('admin')->logout();
    //     $request->session()->forget('admin');
    //     $request->session()->regenerateToken();
    //     return $this->loggedOut($request) ?: redirect()->route('admin.login');
    // }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        return $this->loggedOut($request) ?: redirect()->route('admin.login');
    }

    // Send the response after the user was authenticated
    protected function sendLoginResponse(Request $request)
    {

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);  // Clear login attempts from session

        // Redirect the user to the intended page or a custom page
        // return redirect()->intended($this->redirectTo);

        if ($response = $this->authenticated($request, Auth::guard('admin')->user())) {

            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    // After successful authentication, check user status and last login time
    protected function authenticated(Request $request, $user)
    {
        if ($user->status == 0) {
            Auth::guard('admin')->logout();  // Log out if the user is banned
            return redirect()->route('admin.login')->with('error', 'You are banned from this application. Please contact the system administrator.');
        }

        // Update the last login time
        $user->last_login = Carbon::now();
        $user->save();

        // Determine the user's accessible areas based on roles (if defined)
        $list = collect(config('role'))->pluck(['access', 'view'])->collapse()->intersect($user->admin_access);
        if (count($list) == 0) {
            $list = collect(['admin.profile']);  // Default to profile if no roles match
        }
        // dd(route($list->first()));
        return redirect()->intended(route($list->first()));
    }

    // Clear login attempts manually from the session
    protected function clearLoginAttempts(Request $request)
    {
        $request->session()->forget('login_attempts');
    }

    // Custom method for handling logout success response
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }
}
