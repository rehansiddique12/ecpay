<?php

namespace App\Http\Controllers\Partner;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Gateway;
use App\Models\PartnerLog;
use Carbon\CarbonTimeZone;
use App\Models\Payment;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentLogController extends Controller
{
    public function allReport()
    {
        $log = "View Day Wise Payment & Withdrawal Combine Report";
        $this->addLogs($log);


        $from_date = date('Y-m-01');
        // $from_date = '2023-09-01';
        $to_date = date('Y-m-d');




        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "All Report";
        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
            $website = $main_admin->website;
            $api_id = $main_admin->id;


        $from_date_to_search = date('Y-m-01 00:00:00');
        $to_date_to_search = date('Y-m-d 23:59:59');


        $partnerTimezone = $main_admin->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');

        $paymentsByDate = Payment::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as payment_date"),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as payment_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        )
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
        ->where('api_id', $api_id)
        ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"))
        ->get();

        $payoutsByDate = Payout::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as payout_date"),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as payout_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payout_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payout_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payout_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payout_complete_amount')
        )
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
        ->where('api_id', $api_id)
        ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"))
        ->get();


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count=0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            foreach($paymentsByDate as $key => $payment){
                if ($currentDate == strtotime($payment->payment_date)) {
                    $data[$count]['date'] = $payment->payment_date;
                    $data[$count]['payment_count'] = $payment->payment_count;
                    $data[$count]['payment_total_amount'] = $payment->payment_total_amount;
                    $data[$count]['payment_pending_count'] = $payment->payment_pending_count;
                    $data[$count]['payment_complete_count'] = $payment->payment_complete_count;
                    $data[$count]['payment_pending_amount'] = $payment->payment_pending_amount;
                    $data[$count]['payment_complete_amount'] = $payment->payment_complete_amount;
                }

            }

            foreach($payoutsByDate as $key => $payout){
                if ($currentDate == strtotime($payout->payout_date)) {
                    $data[$count]['date'] = $payout->payout_date;
                    $data[$count]['payout_count'] = $payout->payout_count;
                    $data[$count]['payout_total_amount'] = $payout->payout_total_amount;
                    $data[$count]['payout_pending_count'] = $payout->payout_pending_count;
                    $data[$count]['payout_complete_count'] = $payout->payout_complete_count;
                    $data[$count]['payout_pending_amount'] = $payout->payout_pending_amount;
                    $data[$count]['payout_complete_amount'] = $payout->payout_complete_amount;
                }

            }

            // Increment the date by one day
            $currentDate = strtotime('+1 day', $currentDate);
            $count++;
        }


        // dd($data);

        return view('partner.payment.all_report', compact('data', 'pageTitle','gateways','from_date','to_date'));
    }

    public function reportDetail($date, $gateway, $status)
    {
        $log = "View Day Wise Payment Report Detail";
        $this->addLogs($log);

        $gateways = Gateway::where('status', 1)->get();
        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
        $partnerTimezone = $main_admin->timezone;
            $website = $main_admin->website;
            $api_id = $main_admin->id;

        $page_title = "Payment Report Detail";
        $domains = Api::where('type', 'Admin')->get();

        $heading['date'] = $date;
        $heading['gateway'] = $gateway;
        $heading['status'] = $status;

        if($gateway=="All"){
            $gateway ="";
        }

        if($status=="Pending"){
            $status = 2;
        }elseif($status=="Approved"){
            $status = 1;
        }else{
            $status = "";
        }

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');

        $from_date_to_search = date('Y-m-d H:i:s', strtotime($date . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($date . ' 23:59:59'));


        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);


        $funds = Fund::where('status', '!=', 0)
        ->where('api_id', $api_id)
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
        ->orderBy('id', 'DESC')
        ->with('user', 'gateway', 'payment')
        ->whereHas('payment', function ($query) use ($date, $gateway) {
            $query->where('e_wallet_name', 'like', '%' . $gateway . '%'); // Add the e_wallet_name condition
        })
        ->when($status != -1, function ($query) use ($status) {
            return $query->where('status', 'like', '%' . $status . '%');
        })
        ->get()
        ->map(function ($fund) use ($partnerTimezone) {
            $fund->created_at = \Carbon\Carbon::parse($fund->created_at)->timezone($partnerTimezone);
            $fund->updated_at = \Carbon\Carbon::parse($fund->updated_at)->timezone($partnerTimezone);
            return $fund;
        });

        $funds_t = Fund::where('status', '!=', 0)->where('api_id', $api_id)->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
        ->with('user', 'gateway', 'payment')
        ->whereHas('payment', function ($query) use ($date, $gateway) {
            $query->where('e_wallet_name', 'like', '%' . $gateway . '%'); // Add the e_wallet_name condition
        })
        ->when($status != -1, function ($query) use ($status) {
            return $query->where('status', 'like', '%' . $status . '%');
        })->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);

        return response()->json($funds);

        // return view('partner.payment.reportdetail', compact('funds', 'page_title', 'domains', 'gateways', 'fund_count', 'fund_sum','heading'));
        // return view('partner.payment.reportdetail', compact('funds', 'page_title','domains','gateways','fund_count','fund_sum'));
    }


    public function allReportSearch(Request $request)
    {
        $log = "Search Day Wise Payment & Withdrawal Combine Report";
        $this->addLogs($log);


        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
            $website = $main_admin->website;
            $api_id = $main_admin->id;


        $from_date_to_search = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));


        $partnerTimezone = $main_admin->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');

        $gateways = Gateway::where('status', 1)
            ->get();
        $page_title = "All Report Search";


        // $paymentsQuery = Payment::select(
        //     DB::raw('DATE(completion_at) as date'),
        //     DB::raw('COUNT(*) as payment_count'),
        //     DB::raw('SUM(amount) as payment_total_amount'),
        //     DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
        //     DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
        //     DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
        //     DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        // )
        // ->whereDate('completion_at', '>=', $request->from_date)->whereDate('completion_at', '<=',  $request->to_date)
        // ->where('api_id', $api_id)
        // ->groupBy('date');

        $paymentsQuery = Payment::select(
            DB::raw("DATE(CONVERT_TZ(completion_at, '+06:00', '$offset')) as date"),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as payment_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        )
        ->where('completion_at', '>=', $from_date_to_search)->where('completion_at', '<=', $to_date_to_search)
        ->groupBy(DB::raw("DATE(CONVERT_TZ(completion_at, '+06:00', '$offset'))"));


        if($request->filled('gateway')){
          $paymentsQuery->where('e_wallet_name', $request->gateway);
        }

        $paymentsByDate = $paymentsQuery->get();

        $payoutsQuery = Payout::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as date"),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as payout_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payout_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payout_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payout_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payout_complete_amount')
        )
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=',  $to_date_to_search)
        ->where('api_id', $api_id)
        ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"));

        if($request->filled('gateway')){
          $payoutsQuery->where('e_wallet_name', $request->gateway);
        }

        $payoutsByDate = $payoutsQuery->get();


        $data = [];
        $currentDate = strtotime($request->from_date);
        $endDate = strtotime($request->to_date);

        $count=0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            foreach($paymentsByDate as $key => $payment){
                if ($currentDate == strtotime($payment->date)) {
                    $data[$count]['date'] = $payment->date;
                    $data[$count]['payment_count'] = $payment->payment_count;
                    $data[$count]['payment_total_amount'] = $payment->payment_total_amount;
                    $data[$count]['payment_pending_count'] = $payment->payment_pending_count;
                    $data[$count]['payment_complete_count'] = $payment->payment_complete_count;
                    $data[$count]['payment_pending_amount'] = $payment->payment_pending_amount;
                    $data[$count]['payment_complete_amount'] = $payment->payment_complete_amount;
                }

            }

            foreach($payoutsByDate as $key => $payout){
                if ($currentDate == strtotime($payout->date)) {
                    $data[$count]['date'] = $payout->date;
                    $data[$count]['payout_count'] = $payout->payout_count;
                    $data[$count]['payout_total_amount'] = $payout->payout_total_amount;
                    $data[$count]['payout_pending_count'] = $payout->payout_pending_count;
                    $data[$count]['payout_complete_count'] = $payout->payout_complete_count;
                    $data[$count]['payout_pending_amount'] = $payout->payout_pending_amount;
                    $data[$count]['payout_complete_amount'] = $payout->payout_complete_amount;
                }

            }

            // Increment the date by one day
            $currentDate = strtotime('+1 day', $currentDate);
            $count++;
        }


        // ->where('source', $website)

        $from_date = $request->from_date;
        $to_date = $request->to_date;





        return view('partner.payment.all_report', compact('data', 'page_title','gateways','from_date','to_date'));
    }

    function addLogs($log){

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $user = Auth::guard('partner')->user();

        $partnerlog = new PartnerLog();
        $partnerlog->api_id = $user->id;
        $partnerlog->log = $log;
        $partnerlog->ip_address = $ipAddress;
        $partnerlog->save();
}
}
