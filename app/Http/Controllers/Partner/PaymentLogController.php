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
        $funds = Payment::where('status', '!=', 0)->where('api_id', $api_id)->orderBy('id', 'DESC')->with('user', 'gateway', 'payment')
        ->paginate(config('basic.paginate'));

        $funds_t = Payment::where('status', '!=', 0)->where('api_id', $api_id)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')->first();
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

        $partnerTimezone = $main_admin->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $search['from_date'] = Carbon::parse($search['from_date'], $originalTimezone)->setTimezone($targetTimezone);
        $search['to_date'] = Carbon::parse($search['to_date'], $originalTimezone)->setTimezone($targetTimezone);

        // First get the counts and sums in a separate query
        $aggregates = Payment::where('status', '!=', 0)
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
            ->selectRaw('COUNT(*) as amount_count, SUM(amount) as amount_sum')
            ->first();

        if ($aggregates) {
            $fund_count = $aggregates->amount_count;
            $fund_sum = round($aggregates->amount_sum, 2);
        }

        // Then get the paginated results
        $funds = Payment::where('status', '!=', 0)
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
            ->with('user', 'gateway')
            ->orderBy('id', 'DESC')
            ->paginate(config('basic.paginate'));

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
                    $fund->completion_at
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


        $funds = Payment::where('status', '!=', 0)
    ->where('api_id', $api_id)
    ->where('created_at', '>=', $from_date_to_search)
    ->where('created_at', '<=', $to_date_to_search)
    ->when($gateway, function ($query) use ($gateway) {
        $query->where('e_wallet_name', 'like', '%' . $gateway . '%');
    })
    ->when($status != -1, function ($query) use ($status) {
        return $query->where('status', 'like', '%' . $status . '%');
    })
    ->orderBy('id', 'DESC')
    ->with('user', 'gateway') // Removed payment from with()
    ->get()
    ->map(function ($fund) use ($partnerTimezone) {
        $fund->created_at = \Carbon\Carbon::parse($fund->created_at)->timezone($partnerTimezone);
        $fund->updated_at = \Carbon\Carbon::parse($fund->updated_at)->timezone($partnerTimezone);
        return $fund;
    });
    $funds_t = Payment::where('status', '!=', 0)
    ->where('api_id', $api_id)
    ->where('created_at', '>=', $from_date_to_search)
    ->where('created_at', '<=', $to_date_to_search)
    ->when($gateway, function ($query) use ($gateway) {
        // Replaced payment relation with direct field comparison
        $query->where('e_wallet_name', 'like', '%' . $gateway . '%');
    })
    ->when($status != -1, function ($query) use ($status) {
        return $query->where('status', 'like', '%' . $status . '%');
    })
    ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
    ->with('user', 'gateway') // Removed payment from with()
    ->first();

        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);

        return response()->json($funds);

        // return view('partner.payment.reportdetail', compact('funds', 'page_title', 'domains', 'gateways', 'fund_count', 'fund_sum','heading'));
        // return view('partner.payment.reportdetail', compact('funds', 'page_title','domains','gateways','fund_count','fund_sum'));
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
