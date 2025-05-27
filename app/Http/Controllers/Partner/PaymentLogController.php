<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTimeZone;
use Carbon\Carbon;
use App\Models\Api;
use App\Models\Payout;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\PartnerLog;
use Carbon\CarbonTimeZone;
use App\Http\Traits\Notify;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Facades\App\Services\BasicService;
use Stevebauman\Purify\Facades\Purify;

class PaymentLogController extends Controller
{
    use Notify;

    public function report()
    {
        $log = "View Payment Report";
        $this->addLogs($log);

        $gateways = Gateway::where('status', 1)
            ->get();

        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
        $website = $main_admin->website;
        $api_id = $main_admin->id;

        $partnerTimezone = $main_admin->timezone;

        $pageTitle = "Payment Report";
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'initiate')->where('api_id', $api_id)->orderBy('id', 'DESC')->with(['gateway:id,name,currency,category_id','txn_record:txn_no,partner_transection_id','gateway.category:id,name'])
        ->paginate(config('basic.paginate'));

        $funds_t = Payment::where('status', '!=', 'initiate')->where('api_id', $api_id)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);
        return view('partner.payment.report', compact('funds', 'pageTitle','domains','gateways','fund_count','fund_sum'));
    }

    public function reportSearch(Request $request)
    {
        $log = "Search Payment Report";
        $this->addLogs($log);

        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();

        $website = $main_admin->website;
        $api_id = $main_admin->id;

        $gateways = Gateway::where('status', 1)->get();
        $search = $request->all();
        $fund_count = 0;
        $fund_sum = 0;

        if($search['status']=="All"){
            $search['status'] = "";
        }

        $partnerTimezone = $main_admin->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $search['from_date'] = Carbon::parse($search['from_date'], $originalTimezone)->setTimezone($targetTimezone);
        $search['to_date'] = Carbon::parse($search['to_date'], $originalTimezone)->setTimezone($targetTimezone);


        

            

        if(isset($search['export'])) {
            $exportFunds = Payment::where('status', '!=', 0)
                ->where('api_id', $api_id)
                ->when($search['name'], function ($query) use ($search) {
                    $query->whereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('firstname', 'like', '%' . $search['name'] . '%')
                            ->orWhere('email', 'like', '%' . $search['name'] . '%')
                            ->orWhere('username', 'like', '%' . $search['name'] . '%');
                    });
                })
                ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                    $fromDate = Carbon::parse($search['from_date']);
                    $toDate = Carbon::parse($search['to_date'])->setSecond(59);
                    return $query->where('created_at', '>=', $fromDate)
                                ->where('created_at', '<=', $toDate);
                })
                ->when($search['partner_transection_id'], function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when($search['status'] != 4, function ($query) use ($search) {
                    if ($search['status'] == 99) {
                        $query->where('status', 2)
                              ->where('created_at', '<', Carbon::now()->subMinutes(10));
                    } else if ($search['status'] == 2) {
                        $query->where('status', 2)
                              ->where('created_at', '>=', Carbon::now()->subMinutes(10));
                    } else {
                        $query->where('status', $search['status']);
                    }
                })
                ->when($request->account_no, function ($query) use ($request) {
                    $query->where('account_no', 'LIKE', "%{$request->account_no}%");
                })
                ->when($request->gateway, function ($query) use ($request) {
                    $query->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
                })
                ->orderBy('id', 'DESC')
                ->with('user', 'gateway')
                ->get();

            $data[] = ['Date', 'System Generated Txn', 'Partner Txn', 'Username', 'User-Type', 'Method', 'User-Account-No', 'Amount', 'Charges', 'Final-Amount', 'Status', 'E-Wallet-No', 'Website', 'Source', 'Completed-At'];
            foreach($exportFunds as $fund) {
                $partner_transection_id = ($fund->partner_transection_id!=0) ? $fund->partner_transection_id : '';
                $user_name = "";
                $user_type = "";
                if(optional($fund->user)->username!="dummyuser") {
                    $user_name = optional($fund->user)->username;
                    $user_type = "User";
                } else {
                    $user_name = optional($fund->api)->name;
                    $user_type = optional($fund->api)->acc_type;
                }
                $status = "Pending";
                if($fund->status == 2) {
                    $status = "Pending";
                } elseif($fund->status == 1) {
                    $status = "Completed";
                } elseif($fund->status == 3) {
                    $status = "Rejected";
                }

                $data[] = [
                    $fund->created_at,
                    $fund->transaction,
                    $partner_transection_id,
                    $user_name,
                    $user_type,
                    optional($fund->gateway)->name,
                    $fund->account_no,
                    getAmount($fund->amount),
                    getAmount($fund->charge),
                    getAmount($fund->final_amount),
                    $status,
                    $fund->e_wallet_phone_number,
                    optional($fund->api)->website,
                    $fund->source,
                    $fund->created_at
                ];
            }

            $currentDateTime = date('d_F_Y_h_i_A');
            $csvFileName = "deposit_export_csv_$currentDateTime.csv";
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$csvFileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } else {
            $aggregates = Payment::where('status', 'like', '%' . $search['status'] . '%')
        ->where('api_id', $api_id)
        ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
            $fromDate = Carbon::parse($search['from_date']);
            $toDate = Carbon::parse($search['to_date'])->setSecond(59);
            return $query->where('created_at', '>=', $fromDate)
                        ->where('created_at', '<=', $toDate);
        })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })
           
            ->where(function ($query) use ($request) {
                $query->where('sender', 'LIKE', "%{$request->account_no}%")
                      ->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
            })
            ->select(DB::raw('COUNT(*) as amount_count, SUM(amount) as amount_sum'))
            ->paginate(config('basic.paginate'));

        if (!empty($aggregates) && isset($aggregates[0]->amount_count)) {
            $fund_count = $aggregates[0]->amount_count;
            $fund_sum = round($aggregates[0]->amount_sum, 2);
        }

        // Paginated list of payments
        $funds = Payment::where('status', 'like', '%' . $search['status'] . '%')
        ->where('api_id', $api_id)
        ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
            $fromDate = Carbon::parse($search['from_date']);
            $toDate = Carbon::parse($search['to_date'])->setSecond(59);
            return $query->where('created_at', '>=', $fromDate)
                        ->where('created_at', '<=', $toDate);
        })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })
           
            ->where(function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('sender', 'LIKE', "%{$request->account_no}%")
                        ->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
                });
            })
            ->orderBy('id', 'DESC')
            ->with(['gateway:id,name,currency,category_id','txn_record:txn_no,partner_transection_id','api:id,name,acc_type,website','gateway.category:id,name'])
            ->paginate(config('basic.paginate'));
            
            $pageTitle = "Search Payment Logs";
            return view('partner.payment.report', compact('funds', 'pageTitle', 'gateways', 'fund_count', 'fund_sum'));
        }
    }

    public function dailyReport()
    {
        $log = "View Day Wise Payment Report";
        $this->addLogs($log);

        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
            $website = $main_admin->website;
            $api_id = $main_admin->id;

        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');

        $from_date_to_search = date('Y-m-01 00:00:00');
        $to_date_to_search = date('Y-m-d 23:59:59');


        $partnerTimezone = $main_admin->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');

        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Deposit Report";
        $paymentsByDate = Payment::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as payment_date"),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = 2 THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = 1 THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as complete_amount')
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

        // return view('partner.payment.all_report', compact('data', 'pageTitle','gateways','from_date','to_date'));
        // dd($paymentsByDate);

        return view('partner.payment.daily_report', compact('paymentsByDate', 'pageTitle','gateways','from_date','to_date'));
    }

    public function dailyReportSearch(Request $request)
    {
        $log = "Search Day Wise Payment Report";
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
        $pageTitle = "Daily Deposit Report";
        $query = Payment::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as payment_date"),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = 2 THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = 1 THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as complete_amount')
        )
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
        ->where('api_id', $api_id)
        ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"));


        if($request->filled('gateway')){
            $gateway = strtolower($request->gateway);
            $this_gateway = Gateway::where('name', $gateway)->first();
          $query->where('gateway_id', $this_gateway->id);
        }

        $paymentsByDate = $query->get();

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        return view('partner.payment.daily_report', compact('paymentsByDate', 'pageTitle','gateways','from_date','to_date'));
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

        $pageTitle = "Payment Report Detail";
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


        $funds = Payment::where('status', 'like', '%' . $status . '%')
        ->where('e_wallet_name', 'like', '%' . $gateway . '%')
        ->where('api_id', $api_id)
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
        ->orderBy('id', 'DESC')
        ->with('user', 'gateway')
        ->get()
        ->map(function ($fund) use ($partnerTimezone) {
            $fund->created_at = \Carbon\Carbon::parse($fund->created_at)->timezone($partnerTimezone);
            $fund->updated_at = \Carbon\Carbon::parse($fund->updated_at)->timezone($partnerTimezone);
            return $fund;
        });

        $funds_t = Payment::where('status', 'like', '%' . $status . '%')->where('e_wallet_name', 'like', '%' . $gateway . '%')->where('api_id', $api_id)->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
        ->with('user', 'gateway')
        ->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);

        return response()->json($funds);

        // return view('partner.payment.reportdetail', compact('funds', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum','heading'));
        // return view('partner.payment.reportdetail', compact('funds', 'pageTitle','domains','gateways','fund_count','fund_sum'));
    }

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
        $pageTitle = "All Report Search";


        // $paymentsQuery = Payment::select(
        //     DB::raw('DATE(created_at) as date'),
        //     DB::raw('COUNT(*) as payment_count'),
        //     DB::raw('SUM(amount) as payment_total_amount'),
        //     DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
        //     DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
        //     DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
        //     DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        // )
        // ->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=',  $request->to_date)
        // ->where('api_id', $api_id)
        // ->groupBy('date');

        $paymentsQuery = Payment::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as date"),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as payment_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        )
        ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
        ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"));


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





        return view('partner.payment.all_report', compact('data', 'pageTitle','gateways','from_date','to_date'));
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
