<?php

namespace App\Http\Controllers\Partner;

use App\Models\PartnerLog;
use Illuminate\Http\Request;
use App\Models\TwoStepVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\GoogleAuthenticatorService;

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
}
