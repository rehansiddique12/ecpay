<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        // Middleware ensures only guests can access the login page, except for logout
        // $this->middleware('guest:admin')->except('logout');
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

        // Validate the request
        $this->validateLogin($request);

        // Determine if the user is logging in with email or username
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt to log the user in
        if (Auth::guard('admin')->attempt([$fieldType => $input['username'], 'password' => $input['password']])) {
            return $this->sendLoginResponse($request);
        } else {
            return redirect()->route('admin.login')
                ->with('error', 'Email-Address or Username and Password are wrong.');
        }
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
