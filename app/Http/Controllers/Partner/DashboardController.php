<?php

namespace App\Http\Controllers\Partner;
use DateTimeZone;
use Illuminate\Support\Carbon;
use App\Models\PartnerLog;
use Illuminate\Http\Request;
use App\Models\TwoStepVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\GoogleAuthenticatorService;
use Stevebauman\Purify\Facades\Purify;
use App\Rules\FileTypeValidate;
use \Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

// use App\Http\Traits\Upload;
class DashboardController extends Controller
{
    // use Upload;
    protected $googleAuthenticatorService , $user;
    public function __construct(GoogleAuthenticatorService $googleAuthenticatorService)
    {
        $this->googleAuthenticatorService = $googleAuthenticatorService;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('partner')->user();
            return $next($request);
        });
    }

    public function dashboard()
    {

        $pageTitle = "View Dashboard";
        $user = Auth::guard('partner')->user();
        return view('partner.dashboard', compact('pageTitle', 'user'));

    }

    public function twoFA()
    {
        $partner = $this->user;
        $status = "No";


        $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)
            ->first();
        if ($TwoStepVerification) {
            if ($TwoStepVerification->g_auth_status == "No") {
                // $qrCodeUrl = $this->googleAuthenticatorService->getQRCodeGoogleUrl(env('APP_WEBSITE'), $TwoStepVerification->g_secret_key, $partner->username);
                $urlencoded = ('otpauth://totp/' . env('APP_WEBSITE') . '?secret=' . $TwoStepVerification->g_secret_key . '');
                if (isset($partner->username)) {
                    $urlencoded .= ('&issuer=' . $partner->username);
                }
                $qrCodeUrl = QrCode::size(500)->generate($urlencoded);
                $TwoStepVerification->save();
            } else {
                $status = "Yes";
                $qrCodeUrl = "";
            }
        } else {
            $secret = $this->googleAuthenticatorService->createSecret();
            // $qrCodeUrl = $this->googleAuthenticatorService->getQRCodeGoogleUrl(env('APP_WEBSITE'), $secret, $partner->username);
                $urlencoded = ('otpauth://totp/' . env('APP_WEBSITE') . '?secret=' . $secret . '');
                if (isset($partner->username)) {
                    $urlencoded .= ('&issuer=' . $partner->username);
                }
                $qrCodeUrl = QrCode::size(500)->generate($urlencoded);

            $TwoStepVerification = new TwoStepVerification();
            $TwoStepVerification->g_secret_key = $secret;
            $TwoStepVerification->user_id = $partner->id;
            $TwoStepVerification->g_auth_status = 'No';
            $TwoStepVerification->save();
        }

        $pageTitle = "QR Code Authentication";

        return view('partner.2fa', compact('qrCodeUrl', 'status' , 'pageTitle'));
    }

    public function updateTwoFA(Request $request)
    {
        $partner = $this->user;
        $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)
            ->first();

        $secret_key = $TwoStepVerification->g_secret_key;
        $otp = $request->otp;

        $checkResult = $this->googleAuthenticatorService->verifyCode($secret_key, $otp, 0);
        if ($checkResult) {
            $TwoStepVerification->g_auth_status = 'Yes';
            $TwoStepVerification->save();
            $log = "Enable Two Step Verification";
            $this->addLogs($log);

            return back()->with('success', 'Enabled Successfully.');
        }

        return back()->with('error', 'Wrong OTP.');
    }

    public function disableTwoFA()
    {
        $partner = $this->user;
        $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)
            ->first();
        if ($TwoStepVerification) {
            $TwoStepVerification->delete();
        }
        return back()->with('success', 'Disabled Successfully.');
    }
    function addLogs($log)
    {

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $user = Auth::guard('partner')->user();

        $partnerlog = new PartnerLog();
        $partnerlog->api_id = $user->id;
        $partnerlog->log = $log;
        $partnerlog->ip_address = $ipAddress;
        $partnerlog->save();
    }
    public function profile()
    {

        $log = "View Profile";
        $this->addLogs($log);
        $timezones = DateTimeZone::listIdentifiers();
        $data = [];

        foreach ($timezones as $timezone) {
            $carbon = Carbon::now($timezone);
            $offsetInSeconds = $carbon->getOffset();
            $offsetInHours = $offsetInSeconds / 3600;

            $data[] = [
                'timezone' => $timezone,
                'offset' => $this->formatOffset($offsetInHours)
            ];
        }

        $partner = $this->user;
        $usertimezone = $partner->timezone;
        $pageTitle = 'Partner Profile';
        return view('partner.profile', compact('partner','data','usertimezone' , 'pageTitle'));
    }

    public function formatOffset($offset) {
        $sign = ($offset < 0) ? '-' : '+';
        $hours = floor(abs($offset));
        $minutes = (abs($offset) - $hours) * 60;
        return sprintf("UTC %s%02d:%02d", $sign, $hours, $minutes);
    }

    public function profileUpdate(Request $request)
    {
        $req = Purify::clean($request->except('_token', '_method'));
        $rules = [
            'name' => 'sometimes|required',
            'username' => 'sometimes|required|unique:apis,username,' . $this->user->id,
            'email' => 'sometimes|required|email|unique:apis,email,' . $this->user->id,
            'phone' => 'sometimes|required',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ];

        $log = "Update his Profile";

        $this->addLogs($log);


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = $this->user;
        // dd($user);
        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = $this->uploadImage($request->image, config('location.admin.path'), config('location.admin.size'), $old);
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }
        $user->name = $req['name'];
        $user->username = $req['username'];
        $user->email = $req['email'];
        $user->phone = $req['phone'];
        $user->timezone = $req['timezone'];
        $user->save();

        return back()->with('success', 'Profile Updated Successfully.');
    }

    public function password()
    {
        $pageTitle = 'Password Change';
        return view('partner.password' , compact('pageTitle'));
    }

    public function passwordUpdate(Request $request)
    {
        $req = Purify::clean($request->all());

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $request = (object)$req;
        $user = $this->user;
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', "Password didn't match");
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        $log = "Update Password";
        $this->addLogs($log);


        return back()->with('success', 'Password has been Changed');
    }
}
