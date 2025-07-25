<?php

namespace App\Http\Controllers\Partner;
use DateTimeZone;
use Illuminate\Support\Carbon;
use App\Models\PartnerLog;
use App\Models\Api;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\Commission;
use App\Models\Settlement;
use App\Models\Subscriber;
use App\Models\Ticket;
use App\Models\BetInvest;
use App\Models\Gateway;
use App\Models\User;
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
use Illuminate\Support\Facades\DB;

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
    public function dayList()
    {
        $totalDays = $this->days_in_month(date('m'), date('Y'));
        $daysByMonth = [];
        for ($i = 1; $i <= $totalDays; $i++) {
            array_push($daysByMonth, ['Day ' . sprintf("%02d", $i) => 0]);
        }

        return collect($daysByMonth)->collapse();
    }

    public function days_in_month($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }


    public function dashboard()
    {

        $pageTitle = "Dashboard View";
        $log = "View Dashboard";
        $this->addLogs($log);

        $user = Auth::guard('partner')->user();

        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;

        $website = $user->website;
        $funds_t = Payout::where('status', '!=', 'initiate')->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
            ->where('api_id', $api_id)
            ->first();
        $transection_data['total_payout_count'] = $funds_t->fund_count;
        $transection_data['total_payout_sum'] = (float) number_format($funds_t->fund_sum, 2, '.', '');
        $transection_data['total_payout_charge'] = (float) number_format($funds_t->charge_sum ?? 0, 2, '.', '');

        $currentDate = Carbon::now()->toDateString();

        $funds_today = Payout::where('status', '!=', 'initiate')
            ->whereDate('created_at', $currentDate) // Filter by today's date
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
            ->where('api_id', $api_id)
            ->first();

        $transection_data['total_payout_count_today'] = $funds_today->fund_count ?? 0;
        $transection_data['total_payout_sum_today'] = (float) number_format($funds_today->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_payout_charge_today'] = (float) number_format($funds_today->charge_sum ?? 0, 2, '.', '');


        // Get the first day of the current month
        $firstDayOfMonth = Carbon::now()->startOfMonth()->toDateString();

        // Get the last day of the current month
        $lastDayOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $funds_current_month = Payout::where('status', '!=', 'initiate')
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth]) // Filter by the current month
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
            ->where('api_id', $api_id)
            ->first();

        $transection_data['total_payout_count_current_month'] = $funds_current_month->fund_count ?? 0;
        $transection_data['total_payout_sum_current_month'] = (float) number_format($funds_current_month->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_payout_charge_current_month'] = (float) number_format($funds_current_month->charge_sum ?? 0, 2, '.', '');

        $payments = Payment::where('status', '!=', 'initiate')->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
            ->where('api_id', $api_id)->first();
        $transection_data['total_payment_count'] = $payments->fund_count;
        $transection_data['total_payment_sum'] = (float) number_format($payments->fund_sum, 2, '.', '');
        $transection_data['total_payment_charge'] = (float) number_format($payments->charge_sum, 2, '.', '');


        $currentDate = Carbon::now()->toDateString();

        $payments_today = Payment::where('status', '!=', "initiate")
            ->whereDate('created_at', $currentDate) // Filter by today's date
            ->where('api_id', $api_id)
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
            ->first();

        $transection_data['total_payment_count_today'] = $payments_today->fund_count ?? 0;
        $transection_data['total_payment_sum_today'] = (float) number_format($payments_today->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_payment_charge_today'] = (float) number_format($payments_today->charge_sum ?? 0, 2, '.', '');


        $firstDayOfMonth = Carbon::now()->startOfMonth()->toDateString();

        // Get the last day of the current month
        $lastDayOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $payments_current_month = Payment::where('status', '!=', 'initiate')
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth]) // Filter by the current month
            ->where('api_id', $api_id)
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
            ->first();

        $transection_data['total_payment_count_current_month'] = $payments_current_month->fund_count ?? 0;
        $transection_data['total_payment_sum_current_month'] = (float) number_format($payments_current_month->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_payment_charge_current_month'] = (float) number_format($payments_current_month->charge_sum ?? 0, 2, '.', '');


        $sum = Payout::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('transfer_status', 2)
            ->sum('amount');

        $api_key = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();

        $charge = 0;
        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->withdrawal_percentage * $user->balance / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->withdrawal_percentage * $user->balance / 100;
            }
        }

        $withdrawal_able_amount = $user->balance - $charge;
        $transection_data['withdrawal_able_amount'] = (float) number_format($withdrawal_able_amount ?? 0, 2, '.', '');

        //settlement
        $settlement_total_records = Settlement::where('partner_id', $user->id)->where('status', 1)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charges) as charge_sum')->first();
        $transection_data['total_settlement_count'] = $settlement_total_records->fund_count ?? 0;
        $transection_data['total_settlement_sum'] = (float) number_format($settlement_total_records->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_settlement_charge'] = (float) number_format($settlement_total_records->charge_sum ?? 0, 2, '.', '');

        $settlement_total_records_daily = Settlement::where('partner_id', $user->id)->where('status', 1)->whereDate('created_at', $currentDate)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charges) as charge_sum')->first();
        $transection_data['total_settlement_count_daily'] = $settlement_total_records_daily->fund_count ?? 0;
        $transection_data['total_settlement_sum_daily'] = (float) number_format($settlement_total_records_daily->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_settlement_charge_daily'] = (float) number_format($settlement_total_records_daily->charge_sum ?? 0, 2, '.', '');

        $settlement_total_records_monthly = Settlement::where('partner_id', $user->id)->where('status', 1)->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charges) as charge_sum')->first();
        $transection_data['total_settlement_count_current_month'] = $settlement_total_records_monthly->fund_count ?? 0;
        $transection_data['total_settlement_sum_current_month'] = (float) number_format($settlement_total_records_monthly->fund_sum ?? 0, 2, '.', '');
        $transection_data['total_settlement_charge_current_month'] = (float) number_format($settlement_total_records_monthly->charge_sum ?? 0, 2, '.', '');


        $data['subscriber'] = Subscriber::count();
        $data['funding'] = collect(Payment::selectRaw('SUM(CASE WHEN status = 1 THEN amount END) AS totalAmountReceived')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN charge END) AS totalChargeReceived')
            ->selectRaw('SUM((CASE WHEN created_at >= CURDATE() AND status = 1 THEN amount END)) AS todayDeposit')
            ->get()->toArray())->collapse();

        $data['userRecord'] = 10;


        // $data['tickets'] = collect(Ticket::where('created_at', '>', Carbon::now()->subDays(30))
        //     ->selectRaw('count(CASE WHEN status = 3  THEN status END) AS closed')
        //     ->selectRaw('count(CASE WHEN status = 2  THEN status END) AS replied')
        //     ->selectRaw('count(CASE WHEN status = 1  THEN status END) AS answered')
        //     ->selectRaw('count(CASE WHEN status = 0  THEN status END) AS pending')
        //     ->get()->toArray())->collapse();

        // $dailyInvestAmo = $this->dayList();
        // $dailyInvest = $this->dayList();
        // $dailyReturn = $this->dayList();
        // $dailyRefund = $this->dayList();
        // BetInvest::whereMonth('created_at', Carbon::now()->month)
        //     ->select(
        //         DB::raw('sum(invest_amount) as total_Amount'),
        //         DB::raw('sum(CASE WHEN status != 2 THEN invest_amount END) as Invest_Amount'),
        //         DB::raw('sum(CASE WHEN status = 1 THEN return_amount END) as Return_Amount'),
        //         DB::raw('sum(CASE WHEN status = 2 THEN invest_amount END) as Refund_Amount'),
        //         DB::raw('DATE_FORMAT(created_at,"Day %d") as date')
        //     )
        //     ->groupBy(DB::raw("DATE(created_at)"))
        //     ->get()->map(function ($item) use ($dailyInvestAmo, $dailyInvest, $dailyReturn, $dailyRefund) {
        //         $dailyInvestAmo->put($item['date'], (float) number_format($item['total_Amount'], 2, '.', ''));
        //         $dailyInvest->put($item['date'], (float) number_format($item['Invest_Amount'], 2, '.', ''));
        //         $dailyReturn->put($item['date'], (float) number_format($item['Return_Amount'], 2, '.', ''));
        //         $dailyRefund->put($item['date'], (float) number_format($item['Refund_Amount'], 2, '.', ''));
        //     });

        // $statistics['investment'] = $dailyInvest;
        // $statistics['return'] = $dailyReturn;
        // $statistics['refund'] = $dailyRefund;

        $dailyDeposit = $this->dayList(); // Pre-filled with all days

        Payment::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'Pending')
            ->select(
                DB::raw('SUM(amount) as totalAmount'),
                DB::raw('DATE_FORMAT(created_at, "Day %d") as date')
            )
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "Day %d")'))
            ->get()
            ->each(function ($item) use ($dailyDeposit) {
                if (!empty($item->totalAmount)) {
                    $dailyDeposit->put($item->date, (float) number_format($item->totalAmount, 2, '.', ''));
                }
            });

        $statistics['deposit'] = $dailyDeposit;


        $dailyPayout = $this->dayList();

        Payout::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'Complete')
            ->select(
                DB::raw('SUM(amount) as totalAmount'),
                DB::raw('DATE_FORMAT(created_at, "Day %d") as date')
            )
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "Day %d")'))
            ->get()
            ->each(function ($item) use ($dailyPayout) {
                if (!empty($item->totalAmount)) {
                    $dailyPayout->put($item->date, (float) number_format($item->totalAmount, 2, '.', ''));
                }
            });

        $statistics['payout'] = $dailyPayout;
        $statistics['schedule'] = $this->dayList();


        $gateway = Gateway::count('id');
        $pieLog = collect();

        $data['payout'] = collect(Payout::selectRaw('COUNT(CASE WHEN status = "Pending"  THEN id END) AS pending')
            ->selectRaw('SUM((CASE WHEN status = "Complete" AND created_at >= CURDATE()  THEN amount END)) AS todayPayoutAmount')
            ->selectRaw('SUM((CASE WHEN status = "Complete" AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN amount END)) AS monthlyPayoutAmount')
            ->selectRaw('SUM((CASE WHEN status = "Complete" AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN charge END)) AS monthlyPayoutCharge')
            ->get()->toArray())->collapse();

        $data['latestUser'] = User::latest()->limit(5)->get();

        return view('partner.dashboard', $data, compact('statistics', 'pieLog', 'statistics','pageTitle', 'transection_data'));
    }

    public function twoFA()
    {
        $partner = $this->user;
        $status = "No";
        $secret = $this->googleAuthenticatorService->createSecret();

        $TwoStepVerification = TwoStepVerification::where('user_id', $partner->id)
            ->first();
        if ($TwoStepVerification) {
            if ($TwoStepVerification->g_auth_status == "No") {
                // $qrCodeUrl = $this->googleAuthenticatorService->getQRCodeGoogleUrl(env('APP_WEBSITE'), $TwoStepVerification->g_secret_key, $partner->username);
                $urlencoded = ('otpauth://totp/' . env('APP_WEBSITE') . '?secret=' . $secret . '');
                if (isset($partner->username)) {
                    $urlencoded .= ('&issuer=' . $partner->username);
                }
                $qrCodeUrl = QrCode::size(500)->generate($urlencoded);
                $TwoStepVerification->g_secret_key = $secret;
                $TwoStepVerification->save();
            } else {
                $status = "Yes";
                $qrCodeUrl = "";
            }
        } else {
            
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
        $pageTitle = __('partner_basic.partner_profile');
        return view('partner.profile', compact('partner','data','usertimezone' , 'pageTitle'));
    }


    public function updateTimezone(Request $request)
    {
        $request->validate([
            'timezone' => 'required|timezone',
        ]);

        $user = auth()->user();
        $user->timezone = $request->input('timezone');
        $user->save();

        return redirect()->back()->with('success', 'Timezone updated successfully!');
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
        $pageTitle = __('partner_basic.password_page_title');
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
           return back()->with('error', __('messages.password_mismatch'));
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        $log = "Update Password";
        $this->addLogs($log);


        return back()->with('success', __('messages.password_changed'));
    }
}
