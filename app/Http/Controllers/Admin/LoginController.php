<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\IpWhitelist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TwoStepVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use App\Services\GoogleAuthenticatorService;
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

    // Handle login logic
    public function login(Request $request)
    {
        $data = []; // ✅ initialize so compact('data') won’t break

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

            // Check if 2FA is enabled
            $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)
                ->where('type', 'Admin')
                ->first();

            if ($TwoStepVerification && $TwoStepVerification->g_auth_status == "Yes") {
                if (isset($request->otp)) {
                     $user = Auth::guard('admin')->user();
                    $checkResult = $this->googleAuthenticatorService
                        ->verifyCode($TwoStepVerification->g_secret_key, $request->otp, 0);
                    if ($checkResult) {
                        if (Auth::guard('admin')->attempt([$fieldType => $input['username'], 'password' => $input['password']])) {
                            $ipAddress = $request->ip();
                            $user = Auth::guard('admin')->user();

                            // ✅ IP Whitelist check
                            if (!$this->checkIpWhitelist($user->id, $ipAddress)) {
                                Auth::guard('admin')->logout();
                                return redirect()->route('admin.login')
                                    ->with('error', 'You have no permission to login from this IP: ' . $ipAddress);
                            }

                            return redirect()->intended(route('admin.dashboard'));
                        } else {
                            return redirect()->route('admin.login')
                                ->with('error', 'Email-Address And Password Are Wrong.');
                        }
                    }

                    $data['wrong'] = 'wrong';
                    return view('admin.auth.2fa', compact('data'));
                }
                return view('admin.auth.2fa', compact('data'));
                 }
        }

        // Default login if no 2FA
        if (Auth::guard('admin')->attempt([$fieldType => $input['username'], 'password' => $input['password']])) {
            $ipAddress = $request->ip();
            $user = Auth::guard('admin')->user();

            // ✅ IP Whitelist check
            if (!$this->checkIpWhitelist($user->id, $ipAddress)) {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')
                    ->with('error', 'You have no permission to login from this IP: ' . $ipAddress);
            }

            return redirect()->intended(route('admin.dashboard'));
        } else {
            return redirect()->route('admin.login')
                ->with('error', 'Email-Address And Password Are Wrong.');
        }
    }

    // Username field
    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    // Validate login input
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return $this->loggedOut($request) ?: redirect()->route('admin.login');
    }

    // After successful authentication
    protected function authenticated(Request $request, $user)
    {
        if ($user->status == 0) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error', 'You are banned from this application. Please contact the system administrator.');
        }

        // Update last login
        $user->last_login = Carbon::now();
        $user->save();

        $list = collect(config('role'))->pluck(['access', 'view'])->collapse()->intersect($user->admin_access);
        if (count($list) == 0) {
            $list = collect(['admin.profile']);
        }

        return redirect()->intended(route($list->first()));
    }

    // Clear login attempts
    protected function clearLoginAttempts(Request $request)
    {
        $request->session()->forget('login_attempts');
    }

    // Custom logout response
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }


   private function checkIpWhitelist($userId, $ipAddress)
{
    // Normalize IPv6 localhost to IPv4
    if ($ipAddress === '::1') {
        $ipAddress = '127.0.0.1';
    }

    return IpWhitelist::where('ip_address', $ipAddress)->exists();
}
}
