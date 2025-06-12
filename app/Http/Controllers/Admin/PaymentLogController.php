<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Log;
use App\Models\Txn;
use App\Models\Payout;
use App\Models\SmsLog;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Signature;
use App\Models\Commission;
use App\Models\EWalletLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EWalletCharge;
use App\Models\CronCommission;
use App\Models\EWalletAccount;
use App\Models\PendingPayment;
use Illuminate\Validation\Rule;
use App\Models\ParentCommission;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use App\Models\DailyPartnerSummary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantReportExport;
use App\Models\DailyPartnerSummaryLog;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log as LaravelLog;

class PaymentLogController extends Controller
{

    public function index()
    {
        $pageTitle = __("transaction.payment_logs");
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway','txn_record')->paginate(config('basic.paginate'));
        return view('admin.payment.logs', compact('funds', 'pageTitle', 'domains'));
    }


    public function log2()
    {
        $pageTitle = "Last 1 Hours Payment Logs";
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'initiate')
            ->where('created_at', '>=', Carbon::now()->subMinutes(60))
            ->orderBy('id', 'DESC')
            ->with('gateway', 'txn_record')
            ->paginate(config('basic.paginate'));

        // Step 1: Get all txn_nos from txn_record
        $txnNos = $funds->pluck('txn_record.txn_no')->filter()->unique()->toArray();

        if (!empty($txnNos)) {
            $timeLimit = Carbon::now()->subMinutes(90);

            // Step 2: Bulk fetch related data
            $smsLogs = SmsLog::whereIn('txn', $txnNos)
                ->where('created_at', '>=', $timeLimit)
                ->get()
                ->keyBy('txn');

            $duplicates = Payment::whereIn('txn_id', $txnNos)
                ->where('created_at', '>=', $timeLimit)
                ->get()
                ->keyBy('txn_id');

            $pendingPayments = PendingPayment::whereIn('txn_id', $txnNos)
                ->where('created_at', '>=', $timeLimit)
                ->get()
                ->keyBy('txn_id');
        }

        // Step 3: Loop through and attach results
        foreach ($funds as $fund) {
            $fund->sms_received = 0;
            $fund->duplicate = 0;
            $fund->payment_received = 0;

            if (isset($fund->txn_record) && isset($fund->txn_record->txn_no)) {
                $txn = $fund->txn_record->txn_no;

                if (isset($smsLogs[$txn])) {
                    $fund->sms_received = 1;
                }

                if (isset($duplicates[$txn])) {
                    $fund->duplicate = 1;
                }

                if (isset($pendingPayments[$txn]) && $pendingPayments[$txn]->status == 0) {
                    $fund->payment_received = 1;
                }
            }
        }
        return view('admin.payment.logs2', compact('funds', 'pageTitle', 'domains'));
    }

       public function export_by_logs($from_date)
    {
        $from_date = str_replace('/', '', $from_date); // Remove any slashes if present

        try {
            $sanitizedDate = Carbon::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
        return Excel::download(new MerchantReportExport($from_date), "merchant_report_by_date_{$sanitizedDate}.csv");
    }

    public function apiLog()
    {
        $pageTitle = "3rd Party Deposit Logs";
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        return view('admin.payment.api_log', compact('pageTitle', 'funds', 'domains'));
    }

    public function apisearch(Request $request)
    {
        $search = $request->all();
        $domains = Api::where('type', 'Admin')->get();
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);

        $query = Payment::query();
        if ($request->filled('name')) {
            $query->where('e_wallet_name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('status') && $request->input('status') != '-1') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_time')) {
            $query->whereDate('created_at', $dateSearch);
        }

        if ($request->filled('domain')) {
            $query->where('api_id', $request->input('domain'));
        }
        $funds = $query->orderBy('id', 'DESC')->paginate(config('basic.paginate'));

        $pageTitle = "Search API Payment Logs";
        return view('admin.payment.api_log', compact('funds', 'pageTitle', 'domains'));
    }

    public function apiLogUnclaimed()
    {
        $pageTitle = "Unclaimed Payments";
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'Complete')->orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        return view('admin.payment.unclaimed', compact('pageTitle', 'funds', 'domains'));
    }

    public function apiLogUnclaimedsearch(Request $request)
    {
        $pageTitle = "Unclaimed Payments Search";
        $domains = Api::where('type', 'Admin')->get();

        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);


        $funds = Payment::where('status', '!=', 'Complete')->whereDate('created_at', $dateSearch)->orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        return view('admin.payment.unclaimed', compact('pageTitle', 'funds', 'domains'));
    }

    public function report()
    {
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = __('transaction.payment_report');
        // $today = Carbon::today();
        $today = date('Y-m-d');
        $domains = Api::select('id', 'name', 'website')->where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'initiate')->whereDate('created_at', $today)->orderBy('id', 'DESC')->with(['gateway:id,name,currency,category_id','txn_record:txn_no,partner_transection_id','api:id,name,acc_type,website','gateway.category:id,name'])->paginate(config('basic.paginate'));
        $funds_t = Payment::where('status', '!=', 'initiate')->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')->first();
        // dd($funds_t);
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        return view('admin.payment.report', compact('funds', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum','from_date','to_date'));
    }

    public function reportSearch(Request $request)
    {
        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)->get();
        $search = $request->all();
        $fund_count = 0;
        $fund_sum = 0;
        //$funds = Payment::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));

        $from_date = $search['from_date'];
        $to_date = $search['to_date'];

        if($search['status']=="All"){
            $search['status'] = "";
        }


        // dd($request->all());
        //         exit;


        // Aggregate totals (COUNT & SUM)
        $fund_count = 0;
        $fund_sum = 0;

        if ($request->input('export') == 1) {
            // dd('hello');
            $funds = Payment::where('status', 'like', '%' . $search['status'] . '%')
            ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                return $query->whereDate('created_at', '>=', $search['from_date'])
                            ->whereDate('created_at', '<=', $search['to_date']);
            })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })

            ->when($search['website'], function ($query) use ($search) {
                $query->where('api_id', $search['website']);
            })
            ->where(function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('sender', 'LIKE', "%{$request->account_no}%")
                        ->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
                });
            })
            ->orderBy('id', 'DESC')
            ->with(['gateway:id,name,currency,category_id','txn_record:txn_no,partner_transection_id','api:id,name,acc_type,website','gateway.category:id,name'])
            ->get();






                $data[] = ['Date', 'System Generated Txn', 'E-Wallet Txn', 'Partner Txn', 'User ID' ,'Username', 'User-Type', 'Method', 'User-Account-No', 'Amount', 'Charges', 'Final-Amount', 'Status', 'E-Wallet-No', 'Website', 'Source', 'Completed-At'];
                foreach ($funds as $fund) {
                    // dd($fund);
                    $partner_transection_id = ($fund->partner_transection_id != 0) ? $fund->partner_transection_id : '';
                    // $user_name = "";
                    // $user_type = "";
                    $user_name = optional($fund->api)->name;
                    $user_type = optional($fund->api)->acc_type;
                    $status = "Pending";
                    if ($fund->status == "Pending") {
                        $status = "Pending";
                    } elseif ($fund->status == "Complete") {
                        $status = "Completed";
                    } elseif ($fund->status == "Reject") {
                        $status = "Rejected";
                    }

                    $data[] = [$fund->created_at, $fund->transaction, $fund->txn_id, $partner_transection_id, $fund->member_id  , $user_name, $user_type, optional($fund->gateway)->name, $fund->sender, getAmount($fund->amount), getAmount($fund->charge), getAmount($fund->amount + $fund->charge), $status, $fund->e_wallet_phone_number, optional($fund->api)->website, $fund->request_source, $fund->updated_at];
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





        }else{
            $funds_t = Payment::where('status', 'like', '%' . $search['status'] . '%')
            ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                return $query->whereDate('created_at', '>=', $search['from_date'])
                            ->whereDate('created_at', '<=', $search['to_date']);
            })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })

            ->when($search['website'], function ($query) use ($search) {
                $query->where('api_id', $search['website']);
            })
            ->where(function ($query) use ($request) {
                $query->where('sender', 'LIKE', "%{$request->account_no}%")
                    ->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
            })
            ->select(DB::raw('COUNT(*) as amount_count, SUM(amount) as amount_sum'))
            ->paginate(config('basic.paginate'));

            if (!empty($funds_t) && isset($funds_t[0]->amount_count)) {
                $fund_count = $funds_t[0]->amount_count;
                $fund_sum = round($funds_t[0]->amount_sum, 2);
            }

            // Paginated list of payments
            $funds = Payment::where('status', 'like', '%' . $search['status'] . '%')
            ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                return $query->whereDate('created_at', '>=', $search['from_date'])
                            ->whereDate('created_at', '<=', $search['to_date']);
            })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })

            ->when($search['website'], function ($query) use ($search) {
                $query->where('api_id', $search['website']);
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
        }



        $pageTitle = "Search Payment Logs";
        return view('admin.payment.report', compact('funds', 'pageTitle', 'gateways', 'fund_count', 'fund_sum', 'domains','from_date','to_date'));
    }

    public function reportSearchold(Request $request)
    {
        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)->get();
        $search = $request->all();
        $fund_count = 0;
        $fund_sum = 0;
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);
        // dd($search);
        if ($request->input('export') == 1) {
            // dd('hello');
            $records = Payout::where('status', '!=', 0)
                ->when($search['name'], function ($query) use ($search) {
                    $query->whereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('firstname', 'like', '%' . $search['name'] . '%')
                            ->orWhere('email', 'like', '%' . $search['name'] . '%')
                            ->orWhere('username', 'like', '%' . $search['name'] . '%');
                    });
                })
                ->when(!empty($search['from_date']) && !empty($search['to_date']), function ($query) use ($search) {
                    $query->whereBetween('created_at', [$search['from_date'], $search['to_date']]);
                })
                ->when($search['partner_transection_id'], function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('trx_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    })->orWhereHas('payout', function ($subQuery) use ($search) {
                        $subQuery->where('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when($search['status'] != 4, function ($query) use ($search) {
                    $query->where('status', $search['status']);
                })
                ->orderBy('id', 'DESC')
                ->with('user', 'method', 'payout')
                ->get();
            // dd($funds);
            $data[] = ['Date', 'System Generated Txn', 'E-Wallet Txn', 'Partner Txn', 'Username', 'User-Type', 'Method', 'User-Account-No', 'Amount', 'Charges', 'Final-Amount', 'Request-Status', 'Transfer-Status', 'E-Wallet-No', 'Website', 'Completed-At'];
            foreach ($records as $item) {
                // dd($fund);
                $user_name = "";
                $user_type = "";
                if (optional($item->user)->username != "dummyuser") {
                    $user_name = optional($item->user)->username;
                    $user_type = "User";
                } else {
                    $user_name = optional($item->api)->name;
                    $user_type = optional($item->api)->acc_type;
                }
                $status = "Pending";
                $status2 = "Pending";
                if ($item->transfer_status == 2) {
                    $status = "Approved";
                } elseif ($item->transfer_status == 1) {
                    $status = "Pending";
                } elseif ($item->transfer_status == 3) {
                    $status = "Rejected";
                }

                if ($item->status == "Complete") {
                    $status2 = "Transfered";
                } elseif ($item->status == "Pending") {
                    $status2 = "Transfer Pending";
                } elseif ($item->status == "Reject") {
                    $status2 = "Transfer Rejected";
                }

                $data[] = [$item->created_at, $item->trx_id, $item->txn_id, $item->partner_transection_id, $user_name, $user_type, $item->e_wallet_name, $item->user_account_no, getAmount($item->amount), $item->charge, getAmount($item->amount + $item->charge), $status, $status2, $item->e_wallet_phone_number, $item->source, $item->date_time];
            }

            $currentDateTime = date('d_F_Y_h_i_A');
            $csvFileName = "withdrawal_export_csv_$currentDateTime.csv";
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
        $funds_t = Payment::where('status', '!=', 'initiate')
            ->when($search['name'], function ($query) use ($search) {
                $query->whereHas('user', function ($subQuery) use ($search) {
                    $subQuery->where('firstname', 'like', '%' . $search['name'] . '%')
                        ->orWhere('email', 'like', '%' . $search['name'] . '%')
                        ->orWhere('username', 'like', '%' . $search['name'] . '%');
                });
            })
            ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                return $query->whereDate('created_at', '>=', $search['from_date'])
                             ->whereDate('created_at', '<=', $search['to_date']);
            })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })

            ->when($search['website'], function ($query) use ($search) {
                $query->where('api_id', $search['website']);
            })
            ->where(function ($query) use ($request) {
                $query->where('sender', 'LIKE', "%{$request->account_no}%")
                      ->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
            })
            ->select(DB::raw('COUNT(*) as amount_count, SUM(amount) as amount_sum'))
            ->paginate(config('basic.paginate'));

        if (!empty($funds_t) && isset($funds_t[0]->amount_count)) {
            $fund_count = $funds_t[0]->amount_count;
            $fund_sum = round($funds_t[0]->amount_sum, 2);
        }

        // Paginated list of payments
        $funds = Payment::where('status', 'like', '%' . $search['status'] . '%')
            ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                return $query->whereDate('created_at', '>=', $search['from_date'])
                             ->whereDate('created_at', '<=', $search['to_date']);
            })
            ->when($search['partner_transection_id'], function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })

            ->when($search['website'], function ($query) use ($search) {
                $query->where('api_id', $search['website']);
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
        return view('admin.payment.report', compact('funds', 'pageTitle', 'gateways', 'fund_count', 'fund_sum', 'domains','from_date','to_date'));
    }
}

    public function dailyReport()
    {

        $domains = Api::where('type', 'Admin')->get();

        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Deposit Report";
        $paymentsByDate = Payment::select(
            DB::raw('DATE(created_at) as payment_date'),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as complete_amount')
        )
            ->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        // dd($paymentsByDate);

        return view('admin.payment.daily_report', compact('paymentsByDate', 'pageTitle', 'gateways', 'from_date', 'to_date', 'domains'));
    }

    public function dailyReportSearch(Request $request)
    {
        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Deposit Report";
        $query = Payment::select(
            DB::raw('DATE(created_at) as payment_date'),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as complete_amount')
        )
            ->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->to_date)
            ->when($request->filled('website'), function ($query) use ($request) {
                return $query->where('api_id', $request->website);
            })
            ->groupBy(DB::raw('DATE(created_at)'));


        if ($request->filled('gateway')) {
            $gateway = strtolower($request->gateway);
            $this_gateway = Gateway::where('name', $gateway)->first();
            $query->where('gateway_id', $this_gateway->id);
        }

        $paymentsByDate = $query->get();
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        return view('admin.payment.daily_report', compact('paymentsByDate', 'pageTitle', 'gateways', 'from_date', 'to_date', 'domains'));
    }

    public function allReport()
    {
        $domains = Api::where('type', 'Admin')->get();
        $from_date = date('Y-m-01');
        // $from_date = '2023-09-01';
        $to_date = date('Y-m-d');
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "All Report";

        $paymentsByDate = Payment::select(
            DB::raw('DATE(created_at) as payment_date'),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as payment_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        )
            ->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $payoutsByDate = Payout::select(
            DB::raw('DATE(created_at) as payout_date'),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as payout_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payout_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payout_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payout_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payout_complete_amount')
        )
            ->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            foreach ($paymentsByDate as $key => $payment) {
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

            foreach ($payoutsByDate as $key => $payout) {
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

        return view('admin.payment.all_report', compact('data', 'pageTitle', 'gateways', 'from_date', 'to_date', 'domains'));
    }

    public function reportDetail($date, $gateway, $status)
    {
        $gateways = Gateway::where('status', 1)->get();

        $pageTitle = "Payment Report Detail";
        $domains = Api::where('type', 'Admin')->get();

        $heading['date'] = $date;
        $heading['gateway'] = $gateway;
        $heading['status'] = $status;

        if ($gateway == "All") {
            $gateway = "";
        }

        if ($status == "Pending") {
            $status = 'Pending';
        } elseif ($status == "Complete") {
            $status = 'Complete';
        } else {
            $status = "";
        }



        $funds = Payment::where('status', '!=', 'initiate')
            ->orderBy('id', 'DESC')
            ->with('user', 'gateway')
            ->whereDate('created_at', $date)
            ->where('e_wallet_name', 'like', '%' . $gateway . '%')
            ->when($status != '', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->paginate(config('basic.paginate'));

            $funds_t = Payment::where('status', '!=', 'initiate')
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
            ->with('user', 'gateway')
            ->whereDate('created_at', $date)
            ->where('e_wallet_name', 'like', '%' . $gateway . '%') // Moved this condition here
            ->when($status != '', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->first();

        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);

        return view('admin.payment.reportdetail', compact('funds', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum', 'heading'));
    }


    public function allReportSearch(Request $request)
    {

        $domains = Api::where('type', 'Admin')->get();

        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "All Report Search";

        $paymentsQuery = Payment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as payment_count'),
            DB::raw('SUM(amount) as payment_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payment_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payment_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payment_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payment_complete_amount')
        )
            ->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->to_date)
            ->when($request->filled('website'), function ($query) use ($request) {
                return $query->where('api_id', $request->website);
            })
            ->groupBy(DB::raw('DATE(created_at)'));

        if ($request->filled('gateway')) {
            $paymentsQuery->where('e_wallet_name', $request->gateway);
        }

        $paymentsByDate = $paymentsQuery->get();


        $payoutsQuery = Payout::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as payout_total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as payout_pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as payout_complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as payout_pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as payout_complete_amount')
        )
            ->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->to_date)
            ->when($request->filled('website'), function ($query) use ($request) {
                return $query->where('api_id', $request->website);
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC');

        if ($request->filled('gateway')) {
            $payoutsQuery->where('e_wallet_name', $request->gateway);
        }

        $payoutsByDate = $payoutsQuery->get();


        $data = [];
        $currentDate = strtotime($request->from_date);
        $endDate = strtotime($request->to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            foreach ($paymentsByDate as $key => $payment) {
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

            foreach ($payoutsByDate as $key => $payout) {
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

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        return view('admin.payment.all_report', compact('data', 'pageTitle', 'gateways', 'from_date', 'to_date', 'domains'));
    }

   public function action(Request $request, $id)
    {
        $this->validate($request, [
            'id' => 'required',
            'status' => ['required', Rule::in(['Complete', 'Reject'])],
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $data = Payment::where('id', $request->id)->lockForUpdate()->with('user', 'gateway')->firstOrFail();
            if (!empty($request->sender)) {
                $data->sender = $request->sender;
                $data->save();
            }

            $basic = (object)config('basic');
            $req = Purify::clean($request->all());
            $commit = 0;

            if ($request->status == 'Complete') {

                $account = EWalletAccount::where('e_wallet_name', $data->gateway->code)
                    ->where('account_no', $request->e_wallet_phone_number)
                    ->where('status', 1)
                    ->first();
                if (!$account) {
                    DB::rollBack();
                    throw new \Exception("E-Wallet Account Disable or not Exist.");
                }

                $formattedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->date_time)->format('Y-m-d');
                $formattedTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->date_time)->format('H:i:s');
                $formattedDateTime = Carbon::parse($request->date_time)->format('Y-m-d H:i:s');

                $new = 0;
                if (empty($request->txn_id)) {
                    $request->txn_id = "none";
                    $payment = PendingPayment::where('e_wallet_name', $data->gateway->code)
                    ->where('status', 0)
                        ->where('amount', $data->amount)
                        ->where('sender', $data->account_no)
                        ->whereDate('date', '=', $formattedDate)
                        ->orderBy('id', 'DESC')
                        ->first();
                } else {
                    $check_payment = Payment::where('txn_id', $request->txn_id)
                        ->where('status', 'Complete')
                        ->first();
                    if ($check_payment) {
                        DB::rollBack();
                        throw new \Exception("By This Txn no, Payment Already Completed.");
                    }
                    if ($data->status == "Complete") {
                        DB::rollBack();
                        throw new \Exception("This Payment Already Completed.");
                    }

                    $payment = PendingPayment::where('txn_id', $request->txn_id)->where('status', 0)->orderBy('id', 'DESC')->first();
                    if ($payment) {
                        if ($payment->amount != $data->amount) {
                            throw new \Exception("Wrong TXN.");
                        }
                    }
                }


                if($payment){
                    $check_payment_txn = Payment::where('txn_id', $payment->txn_id)->first();
                    if ($check_payment_txn) {
                        DB::rollBack();
                        throw new \Exception("By This Txn no, Payment Already Completed.");
                    }
                }


                if (!$payment) {
                    // $payment = new Payment();
                    $new = 1;
                }
                else
                {
                    if(empty($data->sender) || $data->sender==0){
                        $data->sender = $payment->sender;
                    }

                    $data->txn_id = $payment->txn_id;
                    $data->date_time = $payment->date_time;
                    $data->transaction_type = $payment->transaction_type;
                    $data->ip_address = $payment->ip_address;
                    $data->e_wallet_type = $payment->e_wallet_type;
                    $data->mac_address = $payment->mac_address;
                    $data->fee = $payment->fee;
                    $data->commission = $payment->commission;
                    $data->e_wallet_charges = $payment->e_wallet_charges;
                    $data->payment_received_at = $payment->created_at;


                    $payment->status = 1;
                    $payment->save();
                    $payment=null;
                    // $payment->delete();
                }
                $payment=$data;
                //dd($payment);

                if ($new == 1) {
                    // $payment->date = $formattedDate;
                    // $payment->time = $formattedTime;
                    $data->date_time = $formattedDateTime;
                }

                $source = "";
                $charge = 0;
                $api_id = "";

                $partner_api_key = Api::where('id', $data->api_id)->lockForUpdate()->first();
                if (!$partner_api_key) {
                    DB::rollBack();
                    throw new \Exception("Partner Api Key Not Found.");
                }
                $amount_to_save = 0;
                if ($partner_api_key) {
                    $source = $partner_api_key->website;
                    $api_id = $partner_api_key->id;

                    if ($source != env('APP_WEBSITE')) {
                        $sum = Payment::whereYear('created_at', now()->year)
                            ->whereMonth('created_at', now()->month)
                            ->where('api_id', $api_id)
                            ->where('status', 'Complete')
                            ->sum('amount');


                        $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                        if ($commissions) {
                            $charge = $commissions->deposit_percentage * $data->amount / 100;
                        } else {
                            $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                            if ($commissions) {
                                $charge = $commissions->deposit_percentage * $data->amount / 100;
                            }
                        }


                        $charge = str_replace(',', '', $charge);
                        $charge = (float)$charge;
                        $charge = round($charge, 2);
                        // $charge = floor($charge * 100) / 100;

                        $net_amount = $data->amount - $charge;
                        $net_amount = round($net_amount, 2);
                        // $net_amount = floor($net_amount * 100) / 100;

                        $partner_api_key->balance += $net_amount;
                        $partner_api_key->save();
                        $amount_to_save = $net_amount;
                    }
                }

                $data->e_wallet_name = $data->gateway->code;
                //$data->amount = $data->amount;
                //$data->sender = $data->account_no;
                $data->txn_id = $request->txn_id;
                $data->transaction_type = 'Received Money';
                $data->e_wallet_phone_number = $request->e_wallet_phone_number;
                $data->e_wallet_type = $request->e_wallet_type;
                //$payment->source = $source;
                //$payment->api_id = $api_id;
                $data->charge = $charge;
                $data->status = 'Complete';
                $data->completed_source = 'AdminPanel';
                //$payment->created_at = $data->created_at;
                $data->trans_complete_date = Carbon::now();
                //$payment->transaction_id = $data->id;
                //$payment->partner_transection_id = $data->partner_transection_id;
                //$payment->member_id = $data->member_id;
                $data->save();

                $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $data->api_id)->whereDate('created_at', '>=', $data->created_at)->get();
                foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                    $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                    $amount_to_update = round($amount_to_update, 2);
                    // $amount_to_update = floor($amount_to_update * 100) / 100;
                    $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                    $DailyPartnerSummary_record->save();

                    $summary_log = new DailyPartnerSummaryLog();
                    $summary_log->partner_id = $partner_api_key->id;
                    $summary_log->partner_balance = $partner_api_key->balance;
                    $summary_log->payment_id = $data->id;
                    $summary_log->total_amount = $net_amount;
                    $summary_log->summary_id = $DailyPartnerSummary_record->id;
                    $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                    $summary_log->source = 'AdminPanel';
                    $summary_log->save();
                }

                if ($new == 1) {
                    $e_wallet_charge = 0;
                    $count_payments = Payment::where('e_wallet_name', $data->gateway->code)->where('status', 'Complete')->where('e_wallet_phone_number', $request->e_wallet_phone_number)->whereDate('date_time', $formattedDate)->count();
                    if ($count_payments >= $account->free_transections_day) {
                        $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->where('from_amount', '<=', $data->amount)->where('to_amount', '>=', $data->amount)->first();
                        if ($e_wallet_charges) {
                            $e_wallet_charge = $e_wallet_charges->charges;
                            if ($e_wallet_charges->charges_type == 2) {
                                $e_wallet_charge = $e_wallet_charges->charges * $data->amount / 100;
                            }
                        } else {
                            $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->orderBy('to_amount', 'desc')->first();
                            if ($e_wallet_charges) {
                                $e_wallet_charge = $e_wallet_charges->charges;
                                if ($e_wallet_charges->charges_type == 2) {
                                    $e_wallet_charge = $e_wallet_charges->charges * $data->amount / 100;
                                }
                            }
                        }
                    }

                    $payment->e_wallet_charges = $e_wallet_charge;
                }

                $payment->save();

                if ($amount_to_save > 0) {
                    $Log = new Log();
                    $Log->date_time = $payment->updated_at;
                    $Log->final_amount = $net_amount;
                    $Log->balance = $partner_api_key->balance;
                    $Log->transection_type = 1;
                    $Log->transection_id = $data->id;
                    $Log->partner_id = $partner_api_key->id;
                    $Log->source = 'AdminPanel';
                    $Log->save();
                }

                $this->updateLimits();
                $account = EWalletAccount::where('e_wallet_name', $data->gateway->code)
                    ->where('account_no', $request->e_wallet_phone_number)
                    ->lockForUpdate()
                    ->first();

                if ($new == 1) {
                    //One E-Wallet Account Log Save
                    $previous_account_balance = number_format($account->balance, 2, '.', '');

                    $account->balance += $data->amount;
                    $account->daily_received += $data->amount;
                    $account->monthly_received += $data->amount;
                    $account->received += $data->amount;
                    $account->save();

                    $e_wallet_log_save = new EWalletLog();
                    $e_wallet_log_save->previous_balance = $previous_account_balance;
                    $e_wallet_log_save->amount = $data->amount;
                    $e_wallet_log_save->charge = 0.00;
                    $e_wallet_log_save->commission = 0.00;
                    $e_wallet_log_save->final_amount = ($data->amount);
                    $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                    $e_wallet_log_save->transaction_type = 1;
                    $e_wallet_log_save->transaction_id = $data->id;
                    $e_wallet_log_save->account_id = $account->id;
                    $e_wallet_log_save->source = 'action';
                    $e_wallet_log_save->save();
                }

                //$data->status = 1;
                $data->feedback = @$req['feedback'];
                //$data->payment_id = $payment->id;
                //$data->created_at = $data->created_at;
                //$data->trans_completed_date = Carbon::now();
                $data->save();

                $PartnerCommissions = PartnerCommission::where('transaction_id', $data->id)->where('type', 1)->where('status', 0)->get();
                foreach ($PartnerCommissions as $PartnerCommission) {
                    $PartnerCommission->status = 1;
                    $PartnerCommission->save();
                    $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
                    if($parent_api_key){
                        $parent_api_key->balance += $PartnerCommission->profit;
                        $parent_api_key->save();

                        $Log = new Log();
                        $Log->date_time = $PartnerCommission->created_at;
                        $Log->final_amount = $PartnerCommission->profit;
                        $Log->balance = $parent_api_key->balance;
                        $Log->transection_type = 5;
                        $Log->transection_id = $PartnerCommission->id;
                        $Log->partner_id = $PartnerCommission->from_id;
                        $Log->source = 'AdminPanel';
                        $Log->save();

                        $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                        foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                            $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($PartnerCommission->profit);
                            $amount_to_update = round($amount_to_update, 2);
                            // $amount_to_update = floor($amount_to_update * 100) / 100;
                            $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                            $DailyPartnerSummary_record->save();

                            $summary_log = new DailyPartnerSummaryLog();
                            $summary_log->partner_id = $parent_api_key->id;
                            $summary_log->partner_balance = $parent_api_key->balance;
                            $summary_log->payment_id = $PartnerCommission->id;
                            $summary_log->total_amount = $PartnerCommission->profit;
                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                            $summary_log->source = 'AdminPanel';
                            $summary_log->save();
                        }
                    }

                }


                // $user = $data->user;
                // $user->balance += $data->amount;
                // $user->save();

                $commit = 1;
                DB::commit();
                $datetime = Carbon::parse($payment->date_time);

                $api_date = $datetime->toDateString();   // '2025-05-19'
                $api_time = $datetime->toTimeString();   // '15:43:00'

                if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {
                    $string_to_hash = json_encode(array(
                        "amount" => strval($this->convertStringToNumber($payment->amount)),
                        "api_key" => $partner_api_key->api_key,
                        "e_wallet_name" => $payment->e_wallet_name,
                        "id" => strval($payment->id),
                        'transaction_type' => 'Deposit',
                        "user_account_no" => strval($payment->sender),
                    ));
                    $secretKey = $partner_api_key->secret_key;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                    $timestamp = time();
                    $combined = $hmac . $timestamp;
                    $sign = base64_encode($combined);

                    $array_data = [
                            'id' => $payment->id,
                            'partner_transection_id' => $payment->partner_transection_id,
                            'transaction_type' => 'Deposit',
                            'e_wallet_name' => $payment->e_wallet_name,
                            'amount' => $this->convertStringToNumber($payment->amount),
                            'user_account_no' => $payment->sender,
                            'txn_id' => $payment->txn_id,
                            'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                            'e_wallet_type' => $payment->e_wallet_type,
                            'charges' => $this->convertStringToNumber($payment->charge),
                            'status' => $payment->status,
                            'completion_date' => $api_date,
                            'completion_time' => $api_time,
                            'created_at' => $payment->created_at,
                            'updated_at' => $payment->updated_at,
                            'sign' => $sign,
                    ];

                    if(!empty($payment->member_id)){
                        $array_data['member_id'] = $payment->member_id;
                    }

                    $requestData = [
                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                        'request_url' => $partner_api_key->api_endpoint_deposit,
                        'request_payload' => json_encode($array_data),
                        'request_headers' => json_encode([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $logId = DB::table('api_logs')->insertGetId($requestData);
                    $csrfToken = csrf_token();
                    $responseData = [];
                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                        ])
                            ->post($partner_api_key->api_endpoint_deposit, $array_data);


                        $responseData = [
                            'response_code' => $response->status(),
                            'response_payload' => $response->body(),
                            'response_headers' => json_encode($response->headers()),
                        ];

                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                    } catch (\Exception $e) {
                        // Ignore the error and do nothing
                    }
                }

                // $remarks = getAmount($data->amount) . ' ' . $basic->currency . ' payment amount has been approved';
                // BasicService::makeTransaction($user, getAmount($data->amount), getAmount($data->charge),  '+', $data->transaction, $remarks);

                // if ($basic->deposit_commission == 1) {
                //     BasicService::setBonus($user, getAmount($data->amount), 'deposit');
                // }
                // $msg = [
                //     'amount' => getAmount($data->amount),
                //     'currency' => $basic->currency,
                // ];
                // $action = [
                //     "link" => '#',
                //     "icon" => "fas fa-money-bill-alt text-white"
                // ];
                // $this->userPushNotification($user, 'PAYMENT_APPROVED', $msg, $action);
                session()->flash('success', 'Approve Successfully');
            } elseif ($request->status == 'Reject') {
                dd($request->all());
                if ($data->status == "Reject") {
                    DB::rollBack();
                    throw new \Exception("This Payment Already Rejected.");
                }

                $data->status = "Reject";
                $data->feedback = $request->feedback;

                $data->update();
                //$user = $data->user;

                $commit = 1;
                DB::commit();

                $partner_api_key = Api::where('id', $data->api_id)->where('type', 'Admin')->first();
                if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                    $string_to_hash = json_encode(array(
                        "amount" => strval($this->convertStringToNumber($data->amount)),
                        "api_key" => $partner_api_key->api_key,
                        "e_wallet_name" => $data->gateway->name,
                        "id" => '',
                        'transaction_type' => 'Deposit',
                        "user_account_no" => strval($data->account_no),

                    ));
                    $secretKey = $partner_api_key->secret_key;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                    $timestamp = time();
                    $combined = $hmac . $timestamp;
                    $sign = base64_encode($combined);

                    $array_data = [
                                'id' => '',
                                'partner_transection_id' => $data->partner_transection_id,
                                'transaction_type' => 'Deposit',
                                'e_wallet_name' => $data->gateway->name,
                                'amount' => $this->convertStringToNumber($data->amount),
                                'user_account_no' => $data->account_no,
                                'txn_id' => '',
                                'e_wallet_phone_number' => $data->e_wallet_phone_number,
                                'e_wallet_type' => '',
                                'charges' => $this->convertStringToNumber($data->charge),
                                'status' => 'Reject',
                                'completion_date' => '',
                                'completion_time' => '',
                                'created_at' => $data->created_at,
                                'updated_at' => $data->updated_at,
                                'sign' => $sign,
                    ];

                    if(!empty($data->member_id)){
                        $array_data['member_id'] = $data->member_id;
                    }

                    $requestData = [
                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                        'request_url' => $partner_api_key->api_endpoint_deposit,
                        'request_payload' => json_encode($array_data),
                        'request_headers' => json_encode([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $logId = DB::table('api_logs')->insertGetId($requestData);

                    $csrfToken = csrf_token();
                    $responseData = [];
                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                        ])
                            ->post($partner_api_key->api_endpoint_deposit, $array_data);
                        $responseData = [
                            'response_code' => $response->status(),
                            'response_payload' => $response->body(),
                            'response_headers' => json_encode($response->headers()),
                        ];

                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                    } catch (\Exception $e) {
                        // Ignore the error and do nothing
                    }
                }


                // $msg = [
                //     'amount' => getAmount($data->amount),
                //     'currency' => $basic->currency,
                //     'feedback' => $data->feedback,
                // ];
                // $action = [
                //     "link" => '#',
                //     "icon" => "fas fa-money-bill-alt text-white"
                // ];
                // $this->userPushNotification($user, 'PAYMENT_REJECTED', $msg, $action);
                session()->flash('success', 'Reject Successfully');

            }
            if($commit==0){
                DB::commit();
            }
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return back();
        }
    }


    public function update_e_wallet(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'e_wallet_phone_number' => 'required',
        ]);
        DB::beginTransaction();
        try {
                $data = Payment::where('id', $request->id)->lockForUpdate()->firstOrFail();
                if($data->status == "Complete" || $data->status == "Reject")
                {
                    DB::rollBack();
                    throw new \Exception("Payment Already Processed.You cannot change e wallet account number.");
                }

                $data->e_wallet_phone_number = $request->e_wallet_phone_number;
                $data->save();




                if ($data) {
                    $pre_e_wallet_phone_number = $data->e_wallet_phone_number;
                    $data->e_wallet_phone_number = $request->e_wallet_phone_number;
                    $data->save();

                    $account = EWalletAccount::where('e_wallet_name', $data->e_wallet_name)
                        ->where('account_no', $pre_e_wallet_phone_number)
                        ->lockForUpdate()
                        ->first();
                    if ($account) {
                        //Two E-Wallet Account Log Save
                        $previous_account_balance = number_format($account->balance, 2, '.', '');

                        $account->balance -= $data->amount;
                        $account->daily_received -= $data->amount;
                        $account->monthly_received -= $data->amount;
                        $account->received -= $data->amount;
                        $account->fee -= $data->fee;
                        $account->commission -= $data->commission;
                        $account->save();

                        $e_wallet_log_save = new EWalletLog();
                        $e_wallet_log_save->previous_balance = $previous_account_balance;
                        $e_wallet_log_save->amount = -$data->amount;
                        $e_wallet_log_save->charge = $data->fee;
                        $e_wallet_log_save->commission = $data->commission;
                        $e_wallet_log_save->final_amount = (-$data->amount + $request->fee - $request->commission);
                        $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                        $e_wallet_log_save->transaction_type = 2;
                        $e_wallet_log_save->transaction_id = $data->id;
                        $e_wallet_log_save->account_id = $account->id;
                        $e_wallet_log_save->source = 'update_e_wallet';
                        $e_wallet_log_save->save();
                    }

                    $account2 = EWalletAccount::where('e_wallet_name', $data->e_wallet_name)
                        ->where('account_no', $request->e_wallet_phone_number)
                        ->lockForUpdate()
                        ->first();

                    if ($account2) {
                        //Four E-Wallet Account Log Save
                        $previous_account2_balance = number_format($account2->balance, 2, '.', '');


                        $account2->balance += $data->amount;
                        $account2->daily_received += $data->amount;
                        $account2->monthly_received += $data->amount;
                        $account2->received += $data->amount;
                        $account2->fee += $data->fee;
                        $account2->commission += $data->commission;
                        $account2->save();

                        $e_wallet_log_save = new EWalletLog();
                        $e_wallet_log_save->previous_balance = $previous_account2_balance;
                        $e_wallet_log_save->amount = $data->amount;
                        $e_wallet_log_save->charge = $data->fee;
                        $e_wallet_log_save->commission = $data->commission;
                        $e_wallet_log_save->final_amount = ($data->amount - $request->fee + $request->commission);
                        $e_wallet_log_save->balance = ($previous_account2_balance + $e_wallet_log_save->final_amount);
                        $e_wallet_log_save->transaction_type = 1;
                        $e_wallet_log_save->transaction_id = $data->id;
                        $e_wallet_log_save->account_id = $account->id;
                        $e_wallet_log_save->source = "update_e_wallet";
                        $e_wallet_log_save->save();
                    }
                }

            DB::commit();
            session()->flash('success', 'Updated Successfully');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return back();
        }

    }

    public function runCallback(Request $request)
    {
        $data = $request->all();
        $payment = Payment::where('id', $request->id)->with('gateway')->latest()->first();
        if ($payment) {
            $api_key = Api::where('id', $payment->api_id)->first();
            // $data = Payment::where('transaction_id', $payment->id)->first();
            if ($payment) {
                if ($api_key && !empty($api_key->api_endpoint_deposit) && $api_key->website != env('APP_WEBSITE')) {
                    $string_to_hash = json_encode(array(
                        "amount" => strval($this->convertStringToNumber($payment->amount)),
                        "api_key" => $api_key->api_key,
                        "e_wallet_name" => $payment->e_wallet_name,
                        "id" => strval($payment->id),
                        'transaction_type' => 'Deposit',
                        "user_account_no" => strval($payment->sender),

                    ));
                    $secretKey = $api_key->secret_key;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                    $timestamp = time();
                    $combined = $hmac . $timestamp;
                    $sign = base64_encode($combined);


                    $array_data = [
                                'id' => $payment->id,
                                'partner_transection_id' => $payment->partner_transection_id,
                                'transaction_type' => 'Deposit',
                                'e_wallet_name' => $payment->e_wallet_name,
                                'amount' => $this->convertStringToNumber($payment->amount),
                                'user_account_no' => $payment->sender,
                                'txn_id' => $payment->txn_id,
                                'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                                'e_wallet_type' => $payment->e_wallet_type,
                                'charges' => $this->convertStringToNumber($payment->charge),
                                'status' => $payment->status,
                                'completion_date' => Carbon::parse($payment->date_time)->toDateString(),
                                'completion_time' => Carbon::parse($payment->date_time)->toTimeString(),
                                'created_at' => $payment->created_at,
                                'updated_at' => $payment->updated_at,
                                'sign' => $sign,
                    ];

                    if(!empty($payment->member_id)){
                        $array_data['member_id'] = $payment->member_id;
                    }


                    $requestData = [
                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                        'request_url' => $api_key->api_endpoint_deposit,
                        'request_payload' => json_encode($array_data),
                        'request_headers' => json_encode([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $logId = DB::table('api_logs')->insertGetId($requestData);

                    $csrfToken = csrf_token();
                    $responseData = [];
                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                        ])
                            ->post($api_key->api_endpoint_deposit, $array_data);


                        $responseData = [
                            'response_code' => $response->status(),
                            'response_payload' => $response->body(),
                            'response_headers' => json_encode($response->headers()),
                        ];

                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                        return response()->json(['status' => 'success', 'message' => 'Callback successfully sent.', 'code' => $responseData['response_code'], 'response_payload' => $responseData['response_payload']], 201);
                    } catch (\Exception $e) {
                        return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => '', 'response_payload' => ''], 200);
                    }
                }
            } else {
                if ($api_key && !empty($api_key->api_endpoint_deposit) && $api_key->website != env('APP_WEBSITE')) {
                    $string_to_hash = json_encode(array(
                        "amount" => strval($this->convertStringToNumber($payment->amount)),
                        "api_key" => $api_key->api_key,
                        "e_wallet_name" => $payment->gateway->name,
                        "id" => '',
                        'transaction_type' => 'Deposit',
                        "user_account_no" => strval($payment->account_no),

                    ));
                    $secretKey = $api_key->secret_key;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                    $timestamp = time();
                    $combined = $hmac . $timestamp;
                    $sign = base64_encode($combined);

                    $array_data = [
                                'id' => '',
                                'partner_transection_id' => $payment->partner_transection_id,
                                'transaction_type' => 'Deposit',
                                'e_wallet_name' => $payment->gateway->name,
                                'amount' => $this->convertStringToNumber($payment->amount),
                                'user_account_no' => $payment->account_no,
                                'txn_id' => '',
                                'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                                'e_wallet_type' => '',
                                'charges' => $this->convertStringToNumber($payment->charge),
                                'status' => 'Reject',
                                'completion_date' => '',
                                'completion_time' => '',
                                'created_at' => $payment->created_at,
                                'updated_at' => $payment->updated_at,
                                'sign' => $sign,
                    ];

                    if(!empty($payment->member_id)){
                        $array_data['member_id'] = $payment->member_id;
                    }

                    $requestData = [
                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                        'request_url' => $api_key->api_endpoint_deposit,
                        'request_payload' => json_encode($array_data),
                        'request_headers' => json_encode([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $logId = DB::table('api_logs')->insertGetId($requestData);

                    $csrfToken = csrf_token();
                    $responseData = [];
                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                        ])
                            ->post($api_key->api_endpoint_deposit, $array_data);
                        $responseData = [
                            'response_code' => $response->status(),
                            'response_payload' => $response->body(),
                            'response_headers' => json_encode($response->headers()),
                        ];

                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                        return response()->json(['status' => 'success', 'message' => 'Callback successfully sent.', 'code' => $responseData['response_code'], 'response_payload' => $responseData['response_payload']], 201);
                    } catch (\Exception $e) {
                        return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => '', 'response_payload' => ''], 200);
                    }
                }
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Unknown Error', 'code' => '', 'response_payload' => ''], 200);
    }

    public function search(Request $request)
    {
        $domains = Api::where('type', 'Admin')->get();
        $search = $request->all();

        // dd($search['status']);
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);


        if (isset($search['export'])) {
            $funds = Payment::where('status', '!=', 'initiate')
                ->when($search['name'], function ($query) use ($search) {
                    $query->whereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('firstname', 'like', '%' . $search['name'] . '%')
                            ->orWhere('email', 'like', '%' . $search['name'] . '%')
                            ->orWhere('username', 'like', '%' . $search['name'] . '%');
                    });
                })
                ->when($search['date_time'], function ($query) use ($search) {
                    $query->whereDate('created_at', $search['date_time']);
                })
                ->when($search['partner_transection_id'], function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when($search['status'] != 'All', function ($query) use ($search) {
                    if ($search['status'] == 99) {
                        // Get records where status is 2 (pending) and created more than 10 minutes ago
                        $query->where('status', "Pending")
                              ->where('created_at', '<', Carbon::now()->subMinutes(10));
                    } else if ($search['status'] == "Pending") {
                        // Get records where status is 2 (pending) and created within the last 10 minutes
                        $query->where('status', "Pending")
                              ->where('created_at', '>=', Carbon::now()->subMinutes(10));
                    } else {
                        // For other statuses, just match the status provided in $search
                        $query->where('status', $search['status']);
                    }
                })

                ->when($search['website'], function ($query) use ($search) {
                    $query->where('api_id', $search['website']);
                })
                ->orderBy('id', 'DESC')
                ->with('user', 'gateway',  'api')
                ->get();
            // dd($funds);
            $data[] = ['Date', 'System Generated Txn', 'E-Wallet Txn', 'Partner Txn', 'Username', 'User-Type', 'Method', 'User-Account-No', 'Amount', 'Charges', 'Final-Amount', 'Status', 'E-Wallet-No', 'Website', 'Source', 'Completed-At'];
            foreach ($funds as $fund) {
                // dd($fund);
                $partner_transection_id = ($fund->partner_transection_id != 0) ? $fund->partner_transection_id : '';
                $user_name = "";
                $user_type = "";
                if (optional($fund->user)->username != "dummyuser") {
                    $user_name = optional($fund->user)->username;
                    $user_type = "User";
                } else {
                    $user_name = optional($fund->api)->name;
                    $user_type = optional($fund->api)->acc_type;
                }
                $status = $fund->status;
                // $status = "Pending";
                // if ($fund->status == 2) {
                //     $status = "Pending";
                // } elseif ($fund->status == 1) {
                //     $status = "Completed";
                // } elseif ($fund->status == 3) {
                //     $status = "Rejected";
                // }
                $data[] = [$fund->created_at, $fund->transaction, $fund->txn_id, $partner_transection_id, $user_name, $user_type, optional($fund->gateway)->name, $fund->account_no, getAmount($fund->amount), getAmount($fund->charge), getAmount($fund->final_amount), $status, $fund->e_wallet_phone_number, optional($fund->api)->website, $fund->source, $fund->updated_at];
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

            $funds = Payment::where('status', '!=', 'initiate')
                ->when($search['name'], function ($query) use ($search) {
                    $query->whereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('firstname', 'like', '%' . $search['name'] . '%')
                            ->orWhere('email', 'like', '%' . $search['name'] . '%')
                            ->orWhere('username', 'like', '%' . $search['name'] . '%');
                    });
                })
                ->when($search['date_time'], function ($query) use ($search) {
                    $query->whereDate('created_at', $search['date_time']);
                })
                ->when($search['partner_transection_id'], function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                  ->when($search['status'] != 'All', function ($query) use ($search) {
                    if ($search['status'] == 99) {
                        // Get records where status is 2 (pending) and created more than 10 minutes ago
                        $query->where('status', "Pending")
                              ->where('created_at', '<', Carbon::now()->subMinutes(10));
                    } else if ($search['status'] == "Pending") {
                        // Get records where status is 2 (pending) and created within the last 10 minutes
                        $query->where('status', "Pending")
                              ->where('created_at', '>=', Carbon::now()->subMinutes(10));
                    } else {
                        // For other statuses, just match the status provided in $search
                        $query->where('status', $search['status']);
                    }
                })
                ->when($search['website'], function ($query) use ($search) {
                    $query->where('api_id', $search['website']);
                })
                ->orderBy('id', 'DESC')
                ->with('user', 'gateway',  'api' ,'txn_record')
                ->paginate(config('basic.paginate'));

            $pageTitle = "Search Payment Logs";
            return view('admin.payment.logs', compact('funds', 'pageTitle', 'domains'));
        }


    }

    public function verifyPayment(Request $request)
    {
        $maxAttempts = 5;
        $attempt = 0;
        $success = 0;

        $partner_transection_id = "";
        if ($request->filled('partner_transection_id')) {
            $partner_transection_id = $request->partner_transection_id;
        }

        $txn_id = "";
        if ($request->filled('txn_id')) {
            $txn_id = $request->txn_id;
        }

        while ($attempt < $maxAttempts && $success==0) {

            LaravelLog::info('Verifypayment try('. $attempt + 1 .') txn_id: '.$txn_id.' partner_txn_id: '.$partner_transection_id);

            try {
                $validator = Validator::make($request->all(), [
                    'api_key' => 'required|string',
                    'txn_id' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 400);
                }

                $partner_transection_id = "";
                if ($request->filled('partner_transection_id')) {
                    $partner_transection_id = $request->partner_transection_id;
                }

                $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->where('status', 1)->first();
                if ($api_key) {
                    $source = $api_key->website;
                    $api_id = $api_key->id;
                    if (empty($source)) {
                        $source = "";
                    }
                    $secretKey = $api_key->secret_key;
                    if ($api_key->sign == 1) {
                        if ($request->filled('sign')) {
                            $string_to_hash = json_encode(array(
                                "api_key" => $request->api_key,
                                "txn_id" => $request->txn_id,
                            ));
                            $hash = hash("sha256", $string_to_hash);
                            $hmac = hash_hmac('sha256', $hash, $secretKey);
                            $timestamp = time();
                            $timestamp_str = (string) $timestamp;
                            $timestamp_length = strlen($timestamp_str);
                            $sign = $request->sign;
                            $decoded = base64_decode($sign);
                            $request_hash = substr($decoded, 0, -$timestamp_length);
                            $sign_timestamp = substr($decoded, -$timestamp_length);
                            if (hash_equals($request_hash, $hmac)) {
                                if ($sign_timestamp >= $timestamp - 60 && $sign_timestamp <= $timestamp + 60) {
                                    $signature = Signature::where('sign', $sign)->first();
                                    if (!$signature) {
                                        $signature = new Signature();
                                        $signature->sign = $sign;
                                        $signature->save();
                                    } else {
                                        return response()->json(['code' => 601, 'message' => 'signature Already Used.'], 404);
                                    }
                                } else {
                                    return response()->json(['code' => 602, 'message' => 'signature Timeout'], 404);
                                }
                            } else {
                                return response()->json(['code' => 603, 'message' => 'Wrong Sign. Data may have been tampered with.'], 404);
                            }
                        } else {
                            return response()->json(['code' => 604, 'message' => 'sign parameter should not be empty.'], 404);
                        }
                    }
                } else {
                    return response()->json(['message' => 'Wrong API key'], 404);
                }


                $Txn = Txn::where('txn_no', $request->txn_id)->where('api_id', $api_id)->orderBy('id', 'DESC')->first();
                if (!$Txn) {
                    $Txn = new Txn();
                    $Txn->txn_no = $request->txn_id;
                    $Txn->partner_transection_id = $partner_transection_id;
                    $Txn->api_id = $api_id;
                    $Txn->save();
                }


                DB::beginTransaction();
                $payment_record = PendingPayment::where('txn_id', $request->txn_id)->where('status', 0)->orderBy('id', 'DESC')->lockForUpdate()->first();
                if (!$payment_record) {
                    return response()->json(['message' => 'Please Wait! Your Payment is Processing.']);
                }else{
                    $check_payment_txn = Payment::where('txn_id', $payment_record->txn_id)->first();
                    if ($check_payment_txn) {
                        DB::rollBack();
                        return response()->json(['message' => 'By This Txn no, Payment Already Completed.']);
                    }
                }

                $currentMonth = now()->format('Y-m');
                $now = Carbon::now();
                $twoHoursAgo = $now->subHours(2);

                $charge = 0;

                $order = Payment::where('partner_transection_id', $partner_transection_id)->where('amount', $payment_record->amount)->where('api_id', $api_id)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                if (!$order) {
                    if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        $order = Payment::where(function ($query) use ($payment_record) {
                            $query->where('sender', 'LIKE', substr($payment_record->sender, 0, 4) . '%')
                                ->where('sender', 'LIKE', '%' . substr($payment_record->sender, -3));
                        })->where('amount', $payment_record->amount)->where('api_id', $api_id)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        if($order){
                            $payment_record->sender = $order->sender;
                        }
                    }elseif (strpos($payment_record->sender, '***') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        $order = Payment::where('sender', 'LIKE', '%' . substr($payment_record->sender, -3))->where('amount', $payment_record->amount)->where('api_id', $api_id)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        if($order){
                            $payment_record->sender = $order->sender;
                        }
                    }else{
                        $order = Payment::where('sender', $payment_record->sender)->where('amount', $payment_record->amount)->where('api_id', $api_id)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    }

                }

                $commit = 0;

                if ($order) {
                    if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        if(!empty($order->sender)){
                            $payment_record->sender = $order->sender;
                        }
                    }elseif (strpos($payment_record->sender, '***') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        if(!empty($order->sender)){
                            $payment_record->sender = $order->sender;
                        }
                    }
                    $partner_api_key = $api_key;

                    if ($source != env('APP_WEBSITE')) {
                        $sum = Payment::whereYear('created_at', now()->year)
                            ->whereMonth('created_at', now()->month)
                            ->where('api_id', $api_id)
                            ->where('status', 'Complete')
                            ->sum('amount');

                        $account = EWalletAccount::where('e_wallet_name', $order->e_wallet_name)
                        ->where('account_no', $order->e_wallet_phone_number)
                        ->first();


                        $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                        if ($commissions) {
                            $charge = $commissions->deposit_percentage * $payment_record->amount / 100;
                        } else {
                            $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                            if ($commissions) {
                                $charge = $commissions->deposit_percentage * $payment_record->amount / 100;
                            }
                        }

                        $charge = str_replace(',', '', $charge);
                        $charge = (float)$charge;
                        $charge = round($charge, 2);

                        $api_balance_row = Api::where('api_key', $request->api_key)->where('type', 'Admin')->lockForUpdate()->first();
                        $net_amount = $payment_record->amount - $charge;
                        $api_balance_row->balance += $net_amount;
                        $api_balance_row->save();

                        $Log = new Log();
                        $Log->date_time = $payment_record->updated_at;
                        $Log->final_amount = $net_amount;
                        $Log->balance = $api_balance_row->balance;
                        $Log->transection_type = 1;
                        $Log->transection_id = $order->id;
                        $Log->partner_id = $api_balance_row->id;
                        $Log->source = 'APIVerify';
                        $Log->save();
                    }

                    $order->status = 'Complete';
                    $order->trans_complete_date = Carbon::now();
                    $order->completed_source = 'APIVerify';
                    $order->charge = $charge;

                    if(empty($order->sender) || $order->sender==0){
                        $order->sender = $payment_record->sender;
                    }

                    $order->txn_id = $payment_record->txn_id;
                    $order->date_time = $payment_record->date_time;
                    $order->transaction_type = $payment_record->transaction_type;
                    $order->ip_address = $payment_record->ip_address;
                    $order->e_wallet_type = $payment_record->e_wallet_type;
                    $order->mac_address = $payment_record->mac_address;
                    $order->fee = $payment_record->fee;
                    $order->commission = $payment_record->commission;
                    $order->e_wallet_charges = $payment_record->e_wallet_charges;
                    $order->payment_received_at = $payment_record->created_at;


                    $payment_record->status = 1;
                    $payment_record->save();
                    $payment_record=null;
                    // $payment_record->delete();
                    $order->save();

                    DB::commit();
                    $commit = 1;

                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $api_id)->whereDate('created_at', '>=', $order->created_at)->get();
                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                        $amount_to_update = round($amount_to_update, 2);
                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                        $DailyPartnerSummary_record->save();

                        $summary_log = new DailyPartnerSummaryLog();
                        $summary_log->partner_id = $partner_api_key->id;
                        $summary_log->partner_balance = $partner_api_key->balance;
                        $summary_log->payment_id = $order->id;
                        $summary_log->total_amount = $net_amount;
                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                        $summary_log->source = 'APIVerify';
                        $summary_log->save();
                    }





                    $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                    foreach ($PartnerCommissions as $PartnerCommission) {
                        $PartnerCommission->status = 1;
                        $PartnerCommission->save();

                        DB::beginTransaction();
                        $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
                        if($parent_api_key){
                            $parent_api_key->balance += $PartnerCommission->profit;
                            $parent_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $PartnerCommission->created_at;
                            $Log->final_amount = $PartnerCommission->profit;
                            $Log->balance = $parent_api_key->balance;
                            $Log->transection_type = 5;
                            $Log->transection_id = $PartnerCommission->id;
                            $Log->partner_id = $PartnerCommission->from_id;
                            $Log->source = 'APIVerify';
                            $Log->save();
                            DB::commit();

                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($PartnerCommission->profit);
                                $amount_to_update = round($amount_to_update, 2);
                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                $DailyPartnerSummary_record->save();

                                $summary_log = new DailyPartnerSummaryLog();
                                $summary_log->partner_id = $parent_api_key->id;
                                $summary_log->partner_balance = $parent_api_key->balance;
                                $summary_log->payment_id = $PartnerCommission->id;
                                $summary_log->total_amount = $PartnerCommission->profit;
                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                $summary_log->source = 'APIVerify';
                                $summary_log->save();
                            }
                        }

                    }

                    if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                        $string_to_hash = json_encode(array(
                            "amount" => strval($this->convertStringToNumber($order->amount)),
                            "api_key" => $partner_api_key->api_key,
                            "e_wallet_name" => $order->e_wallet_name,
                            "id" => strval($order->id),
                            'transaction_type' => 'Deposit',
                            "user_sender" => strval($order->sender),

                        ));
                        $secretKey = $partner_api_key->secret_key;
                        $hash = hash("sha256", $string_to_hash);
                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                        $timestamp = time();
                        $combined = $hmac . $timestamp;
                        $sign = base64_encode($combined);


                        $array_data = [
                                    'id' => $order->id,
                                    'partner_transection_id' => $order->partner_transection_id,
                                    'transaction_type' => 'Deposit',
                                    'e_wallet_name' => $order->e_wallet_name,
                                    'amount' => $this->convertStringToNumber($order->amount),
                                    'user_sender' => $order->sender,
                                    'txn_id' => $order->txn_id,
                                    'e_wallet_phone_number' => $order->e_wallet_phone_number,
                                    'e_wallet_type' => $order->e_wallet_type,
                                    'charges' => $this->convertStringToNumber($order->charge),
                                    'status' => $order->status,
                                    'completion_date' => Carbon::parse($order->date_time)->toDateString(),
                                    'completion_time' => Carbon::parse($order->date_time)->toTimeString(),
                                    'created_at' => $order->created_at,
                                    'updated_at' => $order->updated_at,
                                    'sign' => $sign,
                        ];

                        if(!empty($order->member_id)){
                            $array_data['member_id'] = $order->member_id;
                        }


                        $requestData = [
                            'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                            'request_url' => $partner_api_key->api_endpoint_deposit,
                            'request_payload' => json_encode($array_data),
                            'request_headers' => json_encode([
                                'Content-Type' => 'application/json',
                                'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $logId = DB::table('api_logs')->insertGetId($requestData);
                        try {
                            $csrfToken = Str::random(40);
                            $response = Http::withHeaders([
                                'Content-Type' => 'application/json',
                                'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                            ])
                                ->post($partner_api_key->api_endpoint_deposit, $array_data);

                            if ($response) {
                                $responseData = [
                                    'response_code' => $response->status(),
                                    'response_payload' => $response->body(),
                                    'response_headers' => json_encode($response->headers()),
                                ];

                                DB::table('api_logs')->where('id', $logId)->update($responseData);
                            }
                        } catch (\Exception $e) {
                            //
                        }
                    }
                }

                if($commit == 0){
                    DB::commit();
                }
                return response()->json(['message' => 'Payment Deposited Successfully'], 201);
            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
                return response()->json(['errors' => $e->validator->errors()], 400);
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                }else{
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('Verifypayment Error: txn_id: '.$txn_id.' partner_txn_id: '.$partner_transection_id. ' Error: ' .$e->getMessage());
            }
        }

        return response()->json(['error' => 'An error occurred while processing your request'], 500);
    }

    public function convertStringToNumber($string)
    {
        if (strpos($string, '.') !== false) {
            return (float)$string;
        } else {
            return (int)$string;
        }
    }


    public function addPaymentInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'api_key' => 'required|string',
                'e_wallet_name' => 'required|string',
                'amount' => 'required',
                'sender' => 'nullable|string',
                'txn_id' => 'nullable|string',
                'date' => 'required',
                'time' => 'required',
                'date_time' => 'nullable',
                'transaction_type' => 'required|string',
                'e_wallet_phone_number' => 'nullable|string',
                'ip_address' => 'nullable|string',
                'e_wallet_type' => 'nullable|string',
                'source' => 'nullable|string',
                'mac_address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $api_id = "";
            $api_key = Api::where('api_key', $request->api_key)->where('status', 1)->first();
            if ($api_key && $api_key->website == env('APP_WEBSITE')) {
                $source = $api_key->website;
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Wrong API key'], 404);
            }
            $request_amount = str_replace(',', '', $request->amount);
            $request_amount = (float)$request_amount;
            if ($request->filled('date')) {
                $formattedDate = Carbon::createFromFormat('h:ia d/m/y', $request->date)->format('Y-m-d');
            }
            if ($request->filled('time')) {
                $formattedTime = Carbon::createFromFormat('h:ia d/m/y', $request->time)->format('H:i:s');
            }
            if (is_null($request->date_time)) {
                $formattedDateTime = isset($formattedDate) && isset($formattedTime) ? $formattedDate . ' ' . $formattedTime : null;
            } else {
                $formattedDateTime = Carbon::parse($request->date_time)->format('Y-m-d H:i:s');

            }
            $now = Carbon::now();
            $twoHoursAgo = $now->subHours(2);
            $parsedDateTime = Carbon::parse($formattedDateTime, 'Asia/Dhaka');
            if ($parsedDateTime->lessThan($twoHoursAgo)) {
                $thismessage = "$formattedDateTime is less than two hours ago.";
                return response()->json(['message' => $thismessage], 404);

            }
            DB::beginTransaction();


            LaravelLog::info('e_wallet_name: '.$request->e_wallet_name.' account_no: '.$request->e_wallet_phone_number);

            $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                ->where('account_no', $request->e_wallet_phone_number)
                ->lockForUpdate()
                ->first();
            if (!$account) {
                DB::rollBack();
                return response()->json(['error' => 'E-Wallet Account Disable OR Not Exist'], 500);
            }
            if (empty($request->txn_id)) {
                $request->txn_id = "none";
                $payment_record = Payment::where('e_wallet_name', $request->e_wallet_name)
                    ->where('amount', $request_amount)
                    ->where('sender', $request->sender)
                    ->where('date_time', '=', $formattedDateTime)
                    ->orderBy('id', 'DESC')
                    ->first();
                if ($payment_record) {
                    DB::rollBack();
                    return response()->json(['message' => 'Payment Already Added']);
                }else{
                    $payment_record = PendingPayment::where('e_wallet_name', $request->e_wallet_name)
                    ->where('status', 0)
                        ->where('amount', $request_amount)
                        ->where('sender', $request->sender)
                        ->where('date_time', '=', $formattedDateTime)
                        ->orderBy('id', 'DESC')
                        ->first();
                    if ($payment_record) {
                        DB::rollBack();
                        return response()->json(['message' => 'Payment Already Added']);
                    }
                }
            } else {
                $payment_record = Payment::where('txn_id', $request->txn_id)->orderBy('id', 'DESC')->first();
                if ($payment_record) {
                    DB::rollBack();
                    return response()->json(['message' => 'Payment Already Added']);
                }else{
                    $payment_record = PendingPayment::where('txn_id', $request->txn_id)->where('status', 0)->orderBy('id', 'DESC')->first();
                    if ($payment_record) {
                        DB::rollBack();
                        return response()->json(['message' => 'Payment Already Added']);
                    }
                }
            }
            $partner_txn_verification = 0;
            $Txn = Txn::where('txn_no', $request->txn_id)->orderBy('id', 'DESC')->first();
            if ($Txn) {
                $partner_txn_verification = 1;
                $verify_api_id = $Txn->api_id;
                $partner_transection_id = $Txn->partner_transection_id;
            }
            $charge = 0;
            if ($partner_txn_verification == 1) {
                if (!empty($partner_transection_id)) {
                    $order = Payment::where('api_id', $verify_api_id)->where('partner_transection_id', $partner_transection_id)->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                } else {
                    if (strpos($request->sender, 'XXXX') !== false && $request->mac_address=="111.111.11.111") {
                        $order = Payment::where(function ($query) use ($request) {
                            $query->where('sender', 'LIKE', substr($request->sender, 0, 4) . '%')
                                ->where('sender', 'LIKE', '%' . substr($request->sender, -3));
                        })->where('api_id', $verify_api_id)->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        if($order){
                            $request->sender = $order->sender;
                        }
                    }elseif (strpos($request->sender, '***') !== false && $request->mac_address=="111.111.11.111") {
                        $order = Payment::where('sender', 'LIKE', '%' . substr($request->sender, -3))->where('api_id', $verify_api_id)->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        if($order){
                            $request->sender = $order->sender;
                        }
                    }else{
                        $order = Payment::where('sender', $request->sender)->where('api_id', $verify_api_id)->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    }

                }
            } else {

                    if (strpos($request->sender, 'XXXX') !== false && $request->mac_address=="111.111.11.111") {
                        $order = Payment::where(function ($query) use ($request) {
                            $query->where('sender', 'LIKE', substr($request->sender, 0, 4) . '%')
                                ->where('sender', 'LIKE', '%' . substr($request->sender, -3));
                        })->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        if($order){
                            $request->sender = $order->sender;
                        }
                    }elseif (strpos($request->sender, '***') !== false && $request->mac_address=="111.111.11.111") {
                        $order = Payment::where('sender', 'LIKE', '%' . substr($request->sender, -3))->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        if($order){
                            $request->sender = $order->sender;
                        }
                    }else{
                        $order = Payment::where('sender', $request->sender)->where('amount', $request_amount)->where('status', "Pending")->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    }
            }
            $e_wallet_charge = 0;
            $count_payments = Payment::where('e_wallet_name', $request->e_wallet_name)->where('status', 'Complete')->where('e_wallet_phone_number', $request->e_wallet_phone_number)->whereDate('date_time', $formattedDate)->count();
            if ($count_payments >= $account->free_transections_day) {
                $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->where('from_amount', '<=', $request_amount)->where('to_amount', '>=', $request_amount)->first();
                if ($e_wallet_charges) {
                    $e_wallet_charge = $e_wallet_charges->charges;
                    if ($e_wallet_charges->charges_type == 2) {
                        $e_wallet_charge = $e_wallet_charges->charges * $request->amount / 100;
                    }
                } else {
                    $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->orderBy('to_amount', 'desc')->first();
                    if ($e_wallet_charges) {
                        $e_wallet_charge = $e_wallet_charges->charges;
                        if ($e_wallet_charges->charges_type == 2) {
                            $e_wallet_charge = $e_wallet_charges->charges * $request->amount / 100;
                        }
                    }
                }
            }
                $this->updateLimits();
                if ($request->filled('fee')) {
                    $account->fee += $request->fee;
                }
                $comm = 0;
                if ($request->filled('commission')) {
                    $account->commission += $request->commission;
                    $comm = $request->commission;
                }

                //Three E-Wallet Account Log Save
                $previous_account_balance = number_format($account->balance, 2, '.', '');

                // $account->balance += ($request_amount + $comm);
                $account->balance += $request_amount;
                $account->daily_received += $request_amount;
                $account->monthly_received += $request_amount;
                $account->received += $request_amount;
                $account->save();
                $commit = 0;
            $amount_to_save = 0;
            if ($order) {
                $partner_api_key = Api::where('id', $order->api_id)->where('status', 1)->lockForUpdate()->first();
                if ($partner_api_key) {
                    $source = $partner_api_key->website;
                    $api_id = $partner_api_key->id;
                    if ($partner_api_key->txn_verification == 0 || $partner_txn_verification == 1) {
                        if ($source != env('APP_WEBSITE')) {
                            $sum = Payment::whereYear('created_at', now()->year)
                                ->whereMonth('created_at', now()->month)
                                ->where('api_id', $api_id)
                                ->where('status', 'Complete')
                                ->sum('amount');
                            $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                            if ($commissions) {
                                $charge = $commissions->deposit_percentage * $request_amount / 100;
                            } else {
                                $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                                if ($commissions) {
                                    $charge = $commissions->deposit_percentage * $request_amount / 100;
                                }
                            }
                            $charge = str_replace(',', '', $charge);
                            $charge = (float)$charge;
                            $charge = round($charge, 2);
                            $net_amount = $request_amount - $charge;
                            $partner_api_key->balance += $net_amount;
                            $partner_api_key->save();
                            $amount_to_save = $net_amount;
                        }
                    }
                }
                if ($amount_to_save > 0) {
                    $Log = new Log();
                    $Log->date_time = $order->updated_at;
                    $Log->final_amount = $order->amount - $order->charge;
                    $Log->balance = $partner_api_key->balance;
                    $Log->transection_type = 1;
                    $Log->transection_id = $order->id;
                    $Log->partner_id = $order->api_id;
                    $Log->source = 'APIWithoutVerify';
                    $Log->save();
                }
                if (isset($partner_api_key)) {
                    if ($partner_api_key->txn_verification == 0 || $partner_txn_verification == 1) {
                        if ($order) {
                            $order->status = 'Complete';
                            $order->trans_complete_date = Carbon::now();
                            $order->completed_source = 'APIWithoutVerify';
                            $order->e_wallet_name = $request->e_wallet_name;
                            $order->amount = $request_amount;
                            $order->sender = $request->sender;
                            $order->txn_id = $request->txn_id;
                            $order->date_time = $formattedDateTime;
                            $order->transaction_type = $request->transaction_type;
                            $order->e_wallet_phone_number = $request->e_wallet_phone_number;
                            $order->ip_address = $request->ip();
                            $order->e_wallet_type = $request->e_wallet_type;
                            $order->mac_address = $request->mac_address;
                            $order->payment_received_at = Carbon::now();
                            if ($request->filled('fee')) {
                                $order->fee = $request->fee;
                            }
                            if ($request->filled('commission')) {
                                $order->commission = $request->commission;
                            }
                            $order->e_wallet_charges = $e_wallet_charge;
                            $order->save();
                            $payment = $order;
                            DB::commit();
                            $commit = 1;
                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $order->api_id)->whereDate('created_at', '>=', $order->created_at)->get();
                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                                $amount_to_update = round($amount_to_update, 2);
                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                $DailyPartnerSummary_record->save();
                                $summary_log = new DailyPartnerSummaryLog();
                                $summary_log->partner_id = $partner_api_key->id;
                                $summary_log->partner_balance = $partner_api_key->balance;
                                $summary_log->payment_id = $order->id;
                                $summary_log->total_amount = $net_amount;
                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                $summary_log->source = 'APIWithoutVerify';
                                $summary_log->save();
                            }
                            $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                            foreach ($PartnerCommissions as $PartnerCommission) {
                                $PartnerCommission->status = 1;
                                $PartnerCommission->save();
                                DB::beginTransaction();
                                $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                                if($parent_api_key){
                                    $parent_api_key->balance += $PartnerCommission->profit;
                                    $parent_api_key->save();

                                    $Log = new Log();
                                    $Log->date_time = $PartnerCommission->created_at;
                                    $Log->final_amount = $PartnerCommission->profit;
                                    $Log->balance = $parent_api_key->balance;
                                    $Log->transection_type = 5;
                                    $Log->transection_id = $PartnerCommission->id;
                                    $Log->partner_id = $PartnerCommission->from_id;
                                    $Log->source = 'APIWithoutVerify';
                                    $Log->save();
                                    DB::commit();

                                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($PartnerCommission->profit);
                                        $amount_to_update = round($amount_to_update, 2);
                                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                        $DailyPartnerSummary_record->save();

                                        $summary_log = new DailyPartnerSummaryLog();
                                        $summary_log->partner_id = $parent_api_key->id;
                                        $summary_log->partner_balance = $parent_api_key->balance;
                                        $summary_log->payment_id = $PartnerCommission->id;
                                        $summary_log->total_amount = $PartnerCommission->profit;
                                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                        $summary_log->source = 'APIWithoutVerify';
                                        $summary_log->save();
                                    }
                                }

                            }
                            //curl request only
                            if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {
                                $string_to_hash = json_encode(array(
                                    "amount" => strval($this->convertStringToNumber($payment->amount)),
                                    "api_key" => $partner_api_key->api_key,
                                    "e_wallet_name" => $payment->e_wallet_name,
                                    "id" => strval($payment->id),
                                    'transaction_type' => 'Deposit',
                                    "user_account_no" => strval($payment->sender),
                                ));
                                $secretKey = $partner_api_key->secret_key;
                                $hash = hash("sha256", $string_to_hash);
                                $hmac = hash_hmac('sha256', $hash, $secretKey);
                                $timestamp = time();
                                $combined = $hmac . $timestamp;
                                $sign = base64_encode($combined);
                                $array_data = [
                                            'id' => $payment->id,
                                            'partner_transection_id' => $payment->partner_transection_id,
                                            'transaction_type' => 'Deposit',
                                            'e_wallet_name' => $payment->e_wallet_name,
                                            'amount' => $this->convertStringToNumber($payment->amount),
                                            'user_account_no' => $payment->sender,
                                            'txn_id' => $payment->txn_id,
                                            'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                                            'e_wallet_type' => $payment->e_wallet_type,
                                            'charges' => $this->convertStringToNumber($payment->charge),
                                            'status' => $payment->status,
                                            'completion_date' => Carbon::parse($payment->date_time)->toDateString(),
                                            'completion_time' => Carbon::parse($payment->date_time)->toTimeString(),
                                            'created_at' => $payment->created_at,
                                            'updated_at' => $payment->updated_at,
                                            'sign' => $sign,
                                ];

                                if(!empty($payment->member_id)){
                                    $array_data['member_id'] = $payment->member_id;
                                }
                                $requestData = [
                                    'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                    'request_url' => $partner_api_key->api_endpoint_deposit,
                                    'request_payload' => json_encode($array_data),
                                    'request_headers' => json_encode([
                                        'Content-Type' => 'application/json',
                                        'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                                    ]),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                                $logId = DB::table('api_logs')->insertGetId($requestData);
                                try {
                                    $csrfToken = Str::random(40);
                                    $response = Http::withHeaders([
                                        'Content-Type' => 'application/json',
                                        'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                    ])
                                        ->post($partner_api_key->api_endpoint_deposit, $array_data);

                                    if ($response) {
                                        $responseData = [
                                            'response_code' => $response->status(),
                                            'response_payload' => $response->body(),
                                            'response_headers' => json_encode($response->headers()),
                                        ];

                                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                                    }
                                } catch (\Exception $e) {
                                }
                            }
                        }
                    }
                }
                if($commit == 0){
                    DB::commit();
                }
            }
            if(!isset($payment)){
                $pending_payment = new PendingPayment();
                $pending_payment->e_wallet_name = $request->e_wallet_name;
                $pending_payment->amount = $request_amount;
                $pending_payment->sender = $request->sender;
                $pending_payment->txn_id = $request->txn_id;
                $pending_payment->date_time = $formattedDateTime;
                $pending_payment->transaction_type = $request->transaction_type;
                $pending_payment->e_wallet_phone_number = $request->e_wallet_phone_number;
                $pending_payment->ip_address = $request->ip();
                $pending_payment->e_wallet_type = $request->e_wallet_type;
                $pending_payment->mac_address = $request->mac_address;
                $pending_payment->source = $source;
                if ($request->filled('fee')) {
                    $pending_payment->fee = $request->fee;
                }
                if ($request->filled('commission')) {
                    $pending_payment->commission = $request->commission;
                }
                $pending_payment->e_wallet_charges = $e_wallet_charge;
                $pending_payment->save();
                $payment = $pending_payment;
                $payment->status = "Pending";
            }
            $e_wallet_log_save = new EWalletLog();
            $e_wallet_log_save->previous_balance = $previous_account_balance;
            $e_wallet_log_save->amount = $request_amount;
            $e_wallet_log_save->charge = $request->fee ?? 0;
            $e_wallet_log_save->commission = $request->commission ?? 0;
            $e_wallet_log_save->final_amount = ($request_amount - $request->fee + $request->commission);
            $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
            $e_wallet_log_save->transaction_type = 1;
            $e_wallet_log_save->transaction_id = $payment->id;
            $e_wallet_log_save->account_id = $account->id;
            $e_wallet_log_save->source = "addPaymentInfo";
            $e_wallet_log_save->save();
            if($commit == 0){
                DB::commit();
            }
            return response()->json(['message' => 'Payment information added successfully','id'=>$payment->id,'status'=>$payment->status], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function updateLimits()
    {
        $todayDate = date('Y-m-d');
        $thisMonth = date('m');
        $e_wallet_accounts = EWalletAccount::get();
        foreach ($e_wallet_accounts as $e_wallet_account) {
            if ($e_wallet_account->last_limit_reset != $todayDate) {
                $e_wallet_account->daily_received = 0;
                $e_wallet_account->daily_sent = 0;
            }
            if (date('m', strtotime($e_wallet_account->last_limit_reset)) != $thisMonth) {
                $e_wallet_account->monthly_received = 0;
                $e_wallet_account->monthly_sent = 0;
            }
            $e_wallet_account->last_limit_reset = $todayDate;
            $e_wallet_account->save();
        }
    }


    public function makeatest($id=0){
        $source = "Rocket";
        $acc="01626821906";
        $type="Agent";

        // $this->directwebhookddd($source, $acc, $type);

            // $cron_commissions = ParentCommission::get();
            // foreach ($cron_commissions as $cron_commission) {
            //     $new_commission = Commission::where('id', $cron_commission->commission_id)->first();
            //     if($new_commission){


            //         $cron_commission->from_amount = $new_commission->from_amount;
            //         $cron_commission->to_amount = $new_commission->to_amount;
            //         $cron_commission->type = $new_commission->type;
            //         $cron_commission->gateway_id = $new_commission->gateway_id;
            //         $cron_commission->save();
            //     }

            // }


            // dd(Session::all());

            // if (!Session::has('previousid')) {
            //     Session::put('previousid', 0);
            //     $request->session()->put('aaaaaa', 'xxxxx');
            //     $previousid = 0;
            // } else {
            //     $request->session()->put('aaaaaa', 'nnnnn');
            //     $previousid = Session::get('previousid');
            // }

            // dd(Session::all());

            $previousid = $id;

            $txnIds = PendingPayment::pluck('txn_id');
            $paymentTxnIds = Payment::whereIn('txn_id', $txnIds)->where('txn_id','!=','none')
                        ->pluck('txn_id')
                        ->unique()
                        ->toArray();
                        $commaSeparated = '';
                        foreach ($paymentTxnIds as $key => $paymentTxnId) {
                            $commaSeparated .= $paymentTxnId;

                            // Add comma if it's not the last element
                            if ($key !== array_key_last($paymentTxnIds)) {
                                $commaSeparated .= ',';
                            }

                        }


            dd($commaSeparated);


            $PendingPayments = PendingPayment::select('id','txn_id')->where('id', '>=', $previousid)->limit(100)->get();
            dd($txnIds);
            foreach ($PendingPayments as $PendingPayment) {
                $previousid = $PendingPayment->id;
                // Session::put('previousid', $PendingPayment->id);
                $payment = Payment::select('id','txn_id')->where('txn_id', $PendingPayment->txn_id)->first();
                if($payment){
                    $PendingPayment->status = 1;
                    $PendingPayment->save();
                }

            }


            return view('admin.payment.makeatest', compact('previousid'));

            exit;
    }


    public function directwebhookddd($source, $acc, $type){

        $string = '{"from":"16216","fromName":"","to":"myself","tos":["myself"],"toName":"","toNames":[""],"content":"B2C: Cash-Out from A\/C: ***539 Tk1,000.00 Comm:Tk4.20; A\/C Balance: Tk469,249.21.TxnId: 5233259555 Date:14-MAR-25 06:31:14 am. Download https:\/\/bit.ly\/nexuspay","dir":"incoming","date":"2025-03-14T00:31:15.728Z"}';

        $botToken = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $TestchatId = "-4771016562";
        // $CompletedchatId = "-4754036101";
        // $HoldchatId = "-4735989259";
        // $RejectedchatId = "-4632357788";
        $CompletedchatId = "-4771016562";
        $HoldchatId = "-4771016562";
        $RejectedchatId = "-4771016562";
        // LaravelLog::info('Source:'.$source.' Acc:'.$acc.' Message:'.$string);

            // Step 1: Find the position of "Text"
            $textStart = strpos($string, '"content"');
            $result=[];
            if(isset($textStart) && !empty($textStart)){
                $colonPos = strpos($string, ':', $textStart);
                $valueStart = strpos($string, '"', $colonPos + 1) + 1;
                $valueEnd = strpos($string, '",', $valueStart);
                $text = substr($string, $valueStart, $valueEnd - $valueStart);
                $jsonWithoutText = substr($string, 0, $textStart) . substr($string, $valueEnd + 2);
                $jsonWithoutText = rtrim($jsonWithoutText, ",");
                $array = json_decode($jsonWithoutText, true);
                $result = [];
                if($array['from']=="bKash"){
                    $text = preg_replace('/\s*Download.*$/', '', $text);
                    if (strpos($text, "You have received") === 0) {
                        $t_type = 1;
                        // Extract amount after "Tk" and remove commas
                        if (preg_match('/Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }
                        // Extract customer phone number after "from" and before "Fee"
                        if (preg_match('/from .*?(\d+)\. (?:Fee|Ref)/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }
                        // Extract commission after "Fee" and before "Balance"
                        if (preg_match('/Fee Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['charge'] = 0.00; // Default if not found
                        }
                        // Extract balance after "Balance Tk" and before "TrxID"
                        if (preg_match('/Balance Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['Balance'] = 0.00; // Default if not found
                        }
                        // Extract TrxID after "TrxID" and before "at"
                        if (preg_match('/TrxID (\w+) at/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "at"
                        if (preg_match('/at (.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                        }
                        $result['Comm'] = 0;

                        $result['Comment'] = "You have received";
                    }elseif (strpos($text, "Cash Out") === 0) {

                        if(trim(strtolower($type))=="personal"){
                            $t_type = 2;
                        }else{
                            $t_type = 1;
                        }

                        // Extract amount after "Tk" and remove commas
                        if (preg_match('/Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract customer phone number after "from" and before "Fee"
                        // if (preg_match('/from (.*?) successful/', $text, $matches)) {
                        //     $result['Customer'] = $matches[1];
                        // }

                        if (preg_match('/from (\d{4}[0-9X]{6,10}) successful/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }


                        // Extract commission after "Fee" and before "Balance"
                        if (preg_match('/Fee Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['charge'] = 0.00; // Default if not found
                        }

                        // Extract balance after "Balance Tk" and before "TrxID"
                        if (preg_match('/Balance Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['Balance'] = 0.00; // Default if not found
                        }

                        // Extract TrxID after "TrxID" and before "at"
                        if (preg_match('/TrxID (\w+) at/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "at"
                        if (preg_match('/at (.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                        }

                        $result['Comment'] = "Cash Out";
                        $result['Comm'] = 0;
                    }elseif (strpos($text, "Cash In") === 0) {

                        if(trim(strtolower($type))=="personal"){
                            $t_type = 1;
                        }else{
                            $t_type = 2;
                        }





                        // Extract amount after "Tk" and remove commas
                        if (preg_match('/Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract customer phone number after "from" and before "Fee"
                        // if (preg_match('/from (.*?) successful/', $text, $matches)) {
                        //     $result['Customer'] = $matches[1];
                        // }

                        if (preg_match('/from (\d{4}[0-9X]{6,10}) successful/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }


                        // Extract commission after "Fee" and before "Balance"
                        if (preg_match('/Fee Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['charge'] = 0.00; // Default if not found
                        }

                        // Extract balance after "Balance Tk" and before "TrxID"
                        if (preg_match('/Balance Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['Balance'] = 0.00; // Default if not found
                        }

                        // Extract TrxID after "TrxID" and before "at"
                        if (preg_match('/TrxID (\w+) at/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "at"
                        if (preg_match('/at (.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                        }

                        $result['Comment'] = "Cash Out";
                        $result['Comm'] = 0;
                    }
                }elseif($array['from']=="NAGAD"){
                    $text = str_replace('\n', "\n", $text);
                    $lines = explode("\n", $text);
                    $lines = array_map('trim', $lines);

                    if(count($lines)>0){
                        $comment = $lines[0];

                        $result = [
                            'Comment' => $comment,
                        ];

                        foreach ($lines as $index => $line) {
                            if ($index === count($lines) - 1) {
                                $result['DateTime'] = trim($line);
                            }elseif (strpos($line, ':') !== false) {
                                [$key, $value] = explode(':', $line, 2);
                                $result[trim($key)] = trim($value);
                            }
                        }
                    }
                    if(isset($result['Sender'])){
                        $result['Customer'] = $result['Sender'];
                    }

                    if(!isset($result['Comm'])){
                        $result['Comm'] = 0;
                    }

                    $result['charge'] = 0;

                    if(isset($result['Receiver'])){
                        $result['Customer'] = $result['Receiver'];
                    }

                    if($result['Comment']=="Cash In Successful." || $result['Comment']=="B2B Transfer Successful."){
                        $t_type = 2;
                    }else{
                        $t_type = 1;
                    }

                }elseif($array['from']=="16216"){

                    if (strpos($text, "B2C: Cash-In") === 0) {

                        $t_type = 2;

                        $text = str_replace('\\', "", $text);

                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }elseif (preg_match('/A\/C:\s*(\*+\d+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Comm/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before "Your"
                        if (preg_match('/Comm:Tk([\d,]+\.\d+);/', $text, $matches)) {
                            $result['Comm'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Comm']) || empty($result['Comm'])){
                            if (preg_match('/Comm:Tk([\d,\.]+)/', $text, $commMatches)) {
                                $result['Comm'] = floatval(str_replace(',', '', $commMatches[1]));
                            }
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance:\s*Tk([\d,]+\.\d+)\s*TxnId/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Balance']) || empty($result['Balance'])){
                            if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                                $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                            }
                        }


                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash In";
                        $result['charge'] = 0;

                    }elseif (strpos($text, "B2C: Cash-Out") === 0) {

                        $t_type = 1;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);

                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }elseif (preg_match('/A\/C:\s*(\*+\d+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Comm/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before ";"
                        if (preg_match('/Comm:Tk([\d,\.]+)/', $text, $commMatches)) {
                            $result['Comm'] = floatval(str_replace(',', '', $commMatches[1]));
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                        }

                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash Out";
                        $result['charge'] = 0;


                    }elseif (str_starts_with($text, "Tk") && strpos($text, "received from") !== false) {



                        $t_type = 1;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);





                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Fee/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*received/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before ";"
                        if (preg_match('/Fee:Tk([\d,\.]+)/', $text, $commMatches)) {
                            $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                        }

                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash Received";
                        $result['Comm'] = 0;

                    }elseif (strpos($text, "Cash-In from") === 0) {



                        $t_type = 1;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);



                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Fee/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before "Your"
                        if (preg_match('/Fee: Tk([\d,]+\.\d+);/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['charge']) || empty($result['charge'])){
                            if (preg_match('/Fee: Tk([\d,\.]+)/', $text, $commMatches)) {
                                $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                            }
                        }

                        if(!isset($result['charge']) || empty($result['charge'])){
                            $result['charge'] = 0;
                        }




                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance:\s*Tk([\d,]+\.\d+)\s*TxnId/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Balance']) || empty($result['Balance'])){
                            if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                                $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                            }
                        }


                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash In";
                        $result['Comm'] = 0;





                    }elseif (strpos($text, "Cash-Out to") === 0) {



                        $t_type = 2;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);
                        $text = preg_replace('/\s*download.*$/', '', $text);



                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Fee/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before "Your"
                        if (preg_match('/Fee:Tk([\d,]+\.\d+);/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['charge']) || empty($result['charge'])){
                            if (preg_match('/Fee:Tk([\d,\.]+)/', $text, $commMatches)) {
                                $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                            }
                        }



                        if(!isset($result['charge']) || empty($result['charge'])){
                            $result['charge'] = 0;
                        }




                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance:\s*Tk([\d,]+\.\d+)\s*TxnId/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Balance']) || empty($result['Balance'])){
                            if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                                $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                            }
                        }




                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }




                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = preg_replace('/. Please/i', '', $result['DateTime']);
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash In";
                        $result['Comm'] = 0;





                    }elseif (str_starts_with($text, "Tk") && strpos($text, "transferred to") !== false) {



                        $t_type = 2;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);





                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Fee/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*transferred/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before ";"
                        if (preg_match('/Fee:Tk([\d,\.]+)/', $text, $commMatches)) {
                            $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                        }

                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash Transferred";
                        $result['Comm'] = 0;

                    }
                }






                if(isset($result['Comment']) && isset($result['Amount']) && isset($result['Customer']) && isset($result['TxnID']) && isset($result['Comm']) && isset($result['Balance']) && isset($result['DateTime'])){
                    $result['Amount'] = preg_replace('/[^0-9.]/', '', $result['Amount']);
                    $result['Balance'] = preg_replace('/[^0-9.]/', '', $result['Balance']);
                    $result['Comm'] = preg_replace('/[^0-9.]/', '', $result['Comm']);
                    $result['charge'] = preg_replace('/[^0-9.]/', '', $result['charge']);
                    $result['Amount'] = floatval($result['Amount']);
                    $result['Balance'] = floatval($result['Balance']);
                    $result['Comm'] = floatval($result['Comm']);
                    $result['charge'] = floatval($result['charge']);




                    $account_balance = 0;
                    DB::beginTransaction();
                    $account = EWalletAccount::where('e_wallet_name', $source)->where('account_no', $acc)->lockForUpdate()->first();
                    if($account){
                        $account_balance = $account->live_balance;
                        if($account->type=="Agent" && $account->e_wallet_name=="bKash"){
                            if($result['Comm']==0){
                                $result['Comm'] = ( $result['Amount'] * 0.4 ) / 100;
                            }
                        }
                    }

                    $account_balance = floatval($account_balance);


                    $final_balance_get = 0;
                    $counter = 0;
                    $recordsmatched = 0;
                    $array_t = [];
                    $SmsLogs = SmsLog::where('e_wallet_name', $source)->where('e_wallet_no', $acc)->orderBy('id', 'desc')->take(3)->get()->sortBy('id');
                    $sumMatched = $SmsLogs->sum('matched');
                    if($sumMatched==6){
                        foreach($SmsLogs as $singleSmsLog){
                            $counter++;
                            if($singleSmsLog->type==1){
                                if($counter==1){
                                    $pre_balance = $singleSmsLog->final_amount - $singleSmsLog->amount - $singleSmsLog->comm + $singleSmsLog->charge;
                                }
                                $total_deposit_n = $pre_balance + $singleSmsLog->amount + $singleSmsLog->comm - $singleSmsLog->charge;
                                if(($singleSmsLog->final_amount - $total_deposit_n < 1) && ($total_deposit_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = $singleSmsLog->charge;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }
                            }else{
                                if($counter==1){
                                    $pre_balance = $singleSmsLog->final_amount + $singleSmsLog->amount - $singleSmsLog->comm + $singleSmsLog->charge;
                                }
                                $total_withdrawal_n = $pre_balance - $singleSmsLog->amount + $singleSmsLog->comm - $singleSmsLog->charge;
                                $total_withdrawal2_n = $pre_balance - $singleSmsLog->amount + $singleSmsLog->comm - 5;
                                $total_withdrawal3_n = $pre_balance - $singleSmsLog->amount + $singleSmsLog->comm - 10;
                                if(($singleSmsLog->final_amount - $total_withdrawal_n < 1) && ($total_withdrawal_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = $singleSmsLog->charge;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }elseif(($singleSmsLog->final_amount - $total_withdrawal2_n < 1) && ($total_withdrawal2_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = 5;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }elseif(($singleSmsLog->final_amount - $total_withdrawal3_n < 1) && ($total_withdrawal3_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = 10;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }
                            }
                        }
                    }

                    if($recordsmatched==3){
                        $account_balance = $final_balance_get;
                    }



                    $total_deposit = $account_balance + $result['Amount'] + $result['Comm'] - $result['charge'];
                    $differance  = $total_deposit - $result['Balance'];

                    $total_withdrawal = $account_balance - $result['Amount'] + $result['Comm'] - $result['charge'];
                    $total_withdrawal2 = $account_balance - $result['Amount'] + $result['Comm'] - 5;
                    $total_withdrawal3 = $account_balance - $result['Amount'] + $result['Comm'] - 10;

                    if($t_type==2){
                        $differance  = $total_withdrawal - $result['Balance'];
                    }

                    LaravelLog::info('SMS_Balance:'.$result['Balance'].' Account_Balance:'.$account_balance.' Differance:'.$differance.' Amount:'.$result['Amount'].' Comm:'.$result['Comm'].' Charge:'.$result['charge']);
                    $saved = 2;
                    if($t_type==1){
                        if(($result['Balance'] - $total_deposit < 1) && ($total_deposit - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Deposit Saved!!!!!!!!!');
                            $saved = 1;
                        }
                    }else{
                        if(($result['Balance'] - $total_withdrawal < 1) && ($total_withdrawal - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Withdrawal Saved!!!!!!!!!');
                            $saved = 1;
                        }elseif(($result['Balance'] - $total_withdrawal2 < 1) && ($total_withdrawal2 - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Withdrawal Saved!!!!!!!!!');
                            $result['charge'] = 5;
                            $saved = 1;
                        }elseif(($result['Balance'] - $total_withdrawal3 < 1) && ($total_withdrawal3 - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Withdrawal Saved!!!!!!!!!');
                            $result['charge'] = 10;
                            $saved = 1;
                        }
                    }






                    $Log = new SmsLog();
                    $Log->e_wallet_name = $source;
                    $Log->e_wallet_no = $acc;
                    $Log->customer_acc_no = $result['Customer'];
                    $Log->txn = $result['TxnID'];
                    $Log->account_last_amount = $account_balance;
                    $Log->amount = $result['Amount'];
                    $Log->comm = $result['Comm'];
                    $Log->charge = $result['charge'];
                    $Log->final_amount = $result['Balance'];
                    $Log->type = $t_type;
                    $Log->matched = $saved;
                    $Log->save();


                    DB::commit();





                    $result['DateTime'] = str_replace('\\', "", $result['DateTime']);
                    $date = Carbon::createFromFormat('d/m/Y H:i', $result['DateTime']);
                    $result['DateTime'] = $date->format('h:ia d/m/y');
                    $result['source'] = $source;
                    $result['acc'] = $acc;
                    $result['type'] = $type;



                    if($Log->type==1){
                        $tt_type = "Deposit";
                    }else{
                        $tt_type = "Withdrawal";
                    }

                    if($Log->matched==1){
                        $tt_matched = "Saved";
                        $thischatid = $CompletedchatId;
                    }else{
                        $tt_matched = "On Hold";
                        $thischatid = $HoldchatId;
                    }

                    $formattedDateee = Carbon::parse($Log->created_at)->format('d-m-Y h:i A');



                   $customer_accc = str_replace('*', '⋆', $Log->customer_acc_no);

                    $message = "";
                    $message .= "*$source => $acc => $type* \n";
                    $message .= "*Type:* $tt_type \n";
                    $message .= "*-------------------------------------* \n";
                    $message .= "Customer: $customer_accc \n";
                    $message .= "TXN: $Log->txn \n";
                    $message .= "Amount: $Log->amount \n";
                    $message .= "Comm: $Log->comm \n";
                    $message .= "Charge: $Log->charge \n";
                    $message .= "Final Balance: $Log->final_amount \n";
                    $message .= "DateTime: $formattedDateee \n";
                    $message .= "*-------------------------------------* \n";
                    $message .= "*$tt_matched* \n";



                    $response = Http::post($url, [
                        'chat_id' => $thischatid,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                    ]);

                    dd('abc');


                    if($saved == 2){
                        LaravelLog::info('Balance not match-xxxxxxxxxxxxxxxx');

                    }else{
                        if($recordsmatched==3){
                            foreach($array_t as $array_t_o){
                                $logid = $array_t_o['id'];
                                $SmsLogsingle = SmsLog::where('id', $logid)->first();
                                if($array_t_o['type']==1){
                                    $ttt_type = "Deposit";
                                    $SmsLogsingle->charge = $array_t_o['charge'];
                                    $SmsLogsingle->matched = 1;
                                    $SmsLogsingle->save();
                                    LaravelLog::info('x Deposit Saved txn: '.$array_t_o['txn']);
                                    $parameters = [
                                        "sender" => $array_t_o['customer_acc_no'],
                                        "txn_id" => $array_t_o['txn'],
                                        "amount" => $array_t_o['amount'],
                                        "date" => $result['DateTime'],
                                        "time" => $result['DateTime'],
                                        "transaction_type" => "Payment IN",
                                        "e_wallet_name" => $source,
                                        "e_wallet_phone_number" => $acc,
                                        "mac_address" => "111.111.11.111",
                                        "e_wallet_type" => $type,
                                        "commission" => $array_t_o['comm'],
                                        "fee" => $array_t_o['charge'],
                                        "api_key" => "IaUJUczIxjIJx6JvSPyfTDcYLvz6B86c"
                                    ];

                                    $thisrquest = request()->merge($parameters);

                                    $maxAttempts = 5;
                                    $attempt = 0;
                                    $success = 0;

                                    while ($attempt < $maxAttempts && $success==0) {
                                        $response =  $this->addPaymentInfo($thisrquest);
                                        $content = $response->getContent();
                                        $txn_for_verify = $array_t_o['txn'];
                                        LaravelLog::info('x Deposit Response txn: '. $txn_for_verify .' try('. $attempt + 1 .') '.$content);

                                        if (stripos($content, 'lock') !== false) {
                                            $success = 0;
                                            sleep(1);
                                        }else{
                                            $success = 1;

                                            if (stripos($content, 'pending') !== false) {
                                               $Txn = Txn::where('txn_no', $txn_for_verify)->orderBy('id', 'DESC')->first();
                                                if($Txn){
                                                    $api_key = Api::where('id', $Txn->api_id)->where('type', 'Admin')->first();
                                                    if($api_key){
                                                        $parameters_for_verify = [
                                                            "txn_id" => $txn_for_verify,
                                                            "partner_transection_id" => $Txn->partner_transection_id,
                                                            "api_key" => $api_key->api_key
                                                        ];

                                                        $thisrquest_for_verify = request()->merge($parameters_for_verify);
                                                        $response_for_verify =  $this->verifyPayment($thisrquest_for_verify);
                                                        $content_for_verify = $response_for_verify->getContent();
                                                        LaravelLog::info('x Deposit Verify Response txn: '. $txn_for_verify .' response '.$content_for_verify);
                                                    }
                                                }
                                            }



                                        }

                                        $attempt++;
                                    }




                                }else{
                                    $ttt_type = "Withdrawal";
                                    LaravelLog::info('x Withdrawal Saved txn: '.$array_t_o['txn']);
                                }

                                $formattedDateeee = Carbon::parse($SmsLogsingle->created_at)->format('d-m-Y h:i A');

                                $customer_acccc = str_replace('*', '⋆', $SmsLogsingle->customer_acc_no);

                                $message = "";
                                $message .= "*$source => $acc => $type* \n";
                                $message .= "*Type:* $ttt_type \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "Customer: $customer_acccc \n";
                                $message .= "TXN: $SmsLogsingle->txn \n";
                                $message .= "Amount: $SmsLogsingle->amount \n";
                                $message .= "Comm: $SmsLogsingle->comm \n";
                                $message .= "Charge: $SmsLogsingle->charge \n";
                                $message .= "Final Balance: $SmsLogsingle->final_amount \n";
                                $message .= "DateTime: $formattedDateeee \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "*Holded SMS Saved* \n";

                                $response = Http::post($url, [
                                    'chat_id' => $CompletedchatId,
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                ]);

                            }
                        }

                    }








                    $SmsLog = SmsLog::where('e_wallet_name', $source)->where('e_wallet_no', $acc)->where('matched', 2)->orderBy('id', 'desc')->first();
                    if($SmsLog){
                        if($SmsLog->type==1){
                            $previous_total_deposit = $result['Balance'] + $SmsLog->amount + $SmsLog->comm - $SmsLog->charge;
                            if(($SmsLog->final_amount - $previous_total_deposit < 1) && ($previous_total_deposit - $SmsLog->final_amount < 1)){
                                $SmsLog->matched = 1;
                                $SmsLog->save();

                                LaravelLog::info('Previous Deposit Saved!!!!!!!!!');

                                DB::beginTransaction();
                                $accountt = EWalletAccount::where('e_wallet_name', $source)->where('account_no', $acc)->lockForUpdate()->first();
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $accountt->save();
                                }
                                DB::commit();

                                $parameters = [
                                    "sender" => $SmsLog->customer_acc_no,
                                    "txn_id" => $SmsLog->txn,
                                    "amount" => $SmsLog->amount,
                                    "date" => $result['DateTime'],
                                    "time" => $result['DateTime'],
                                    "transaction_type" => $result['Comment'],
                                    "e_wallet_name" => $source,
                                    "e_wallet_phone_number" => $acc,
                                    "mac_address" => "111.111.11.111",
                                    "e_wallet_type" => $type,
                                    "commission" => $SmsLog->comm,
                                    "fee" => $SmsLog->charge,
                                    "api_key" => "IaUJUczIxjIJx6JvSPyfTDcYLvz6B86c"
                                ];

                                $thisrquest = request()->merge($parameters);

                                $maxAttempts = 5;
                                $attempt = 0;
                                $success = 0;

                                while ($attempt < $maxAttempts && $success==0) {
                                    $response =  $this->addPaymentInfo($thisrquest);
                                    $content = $response->getContent();
                                    $txn_for_verify = $SmsLog->txn;
                                    LaravelLog::info('Direct IFTTT Response Previous txn: '. $txn_for_verify .' try('. $attempt + 1 .') '.$content);

                                    if (stripos($content, 'lock') !== false) {
                                        $success = 0;
                                        sleep(1);
                                    }else{
                                        $success = 1;

                                        if (stripos($content, 'pending') !== false) {
                                            $Txn = Txn::where('txn_no', $txn_for_verify)->orderBy('id', 'DESC')->first();
                                            if($Txn){
                                                $api_key = Api::where('id', $Txn->api_id)->where('type', 'Admin')->first();
                                                if($api_key){
                                                    $parameters_for_verify = [
                                                        "txn_id" => $txn_for_verify,
                                                        "partner_transection_id" => $Txn->partner_transection_id,
                                                        "api_key" => $api_key->api_key
                                                    ];

                                                    $thisrquest_for_verify = request()->merge($parameters_for_verify);
                                                    $response_for_verify =  $this->verifyPayment($thisrquest_for_verify);
                                                    $content_for_verify = $response_for_verify->getContent();
                                                    LaravelLog::info('x Deposit Verify Response txn: '. $txn_for_verify .' response '.$content_for_verify);
                                                }
                                            }
                                        }
                                    }

                                    $attempt++;
                                }


                                $formattedDateeeee = Carbon::parse($SmsLog->created_at)->format('d-m-Y h:i A');

                                $message = "";
                                $message .= "*$source => $acc => $type* \n";
                                $message .= "*Type:* Deposit \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "Customer: $SmsLog->customer_acc_no \n";
                                $message .= "TXN: $SmsLog->txn \n";
                                $message .= "Amount: $SmsLog->amount \n";
                                $message .= "Comm: $SmsLog->comm \n";
                                $message .= "Charge: $SmsLog->charge \n";
                                $message .= "Final Balance: $SmsLog->final_amount \n";
                                $message .= "DateTime: $formattedDateeeee \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "*Holded SMS Saved* \n";

                                $response = Http::post($url, [
                                    'chat_id' => $CompletedchatId,
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                ]);
                            }
                        }else{


                            $previous_withdrawal = $result['Balance'] - $SmsLog->amount + $SmsLog->comm - $SmsLog->charge;
                            $previous_withdrawal2 = $result['Balance'] - $SmsLog->amount + $SmsLog->comm - 5;
                            $previous_withdrawal3 = $result['Balance'] - $SmsLog->amount + $SmsLog->comm - 10;

                            DB::beginTransaction();
                            $accountt = EWalletAccount::where('e_wallet_name', $source)->where('account_no', $acc)->lockForUpdate()->first();
                            $p_matched = 2;
                            if(($SmsLog->final_amount - $previous_withdrawal < 1) && ($previous_withdrawal - $SmsLog->final_amount < 1)){
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $accountt->save();
                                }
                                $p_matched = 1;
                                LaravelLog::info('Previous Withdrawal Saved!!!!!!!!!');
                            }elseif(($SmsLog->final_amount - $previous_withdrawal2 < 1) && ($previous_withdrawal2 - $SmsLog->final_amount < 1)){
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $SmsLog->charge = 5;
                                    $accountt->save();
                                }
                                $p_matched = 1;
                                LaravelLog::info('Previous Withdrawal Saved!!!!!!!!!');

                            }elseif(($SmsLog->final_amount - $previous_withdrawal3 < 1) && ($previous_withdrawal3 - $SmsLog->final_amount < 1)){
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $SmsLog->charge = 10;
                                    $accountt->save();
                                }
                                $p_matched = 1;
                                LaravelLog::info('Previous Withdrawal Saved!!!!!!!!!');
                            }

                            DB::commit();


                            $SmsLog->matched = $p_matched;
                            $SmsLog->save();

                            if($p_matched==1){
                                $formattedDateeeeee = Carbon::parse($SmsLog->created_at)->format('d-m-Y h:i A');

                                $message = "";
                                $message .= "*$source => $acc => $type* \n";
                                $message .= "*Type:* Withdrawal \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "Customer: $SmsLog->customer_acc_no \n";
                                $message .= "TXN: $SmsLog->txn \n";
                                $message .= "Amount: $SmsLog->amount \n";
                                $message .= "Comm: $SmsLog->comm \n";
                                $message .= "Charge: $SmsLog->charge \n";
                                $message .= "Final Balance: $SmsLog->final_amount \n";
                                $message .= "DateTime: $formattedDateeeeee \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "*Holded SMS Saved* \n";

                                $response = Http::post($url, [
                                    'chat_id' => $CompletedchatId,
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                ]);
                            }






                        }

                    }




                    if(($result['Balance'] - $total_deposit < 1) && ($total_deposit - $result['Balance'] < 1)){
                        $parameters = [
                            "sender" => $result['Customer'],
                            "txn_id" => $result['TxnID'],
                            "amount" => $result['Amount'],
                            "date" => $result['DateTime'],
                            "time" => $result['DateTime'],
                            "transaction_type" => $result['Comment'],
                            "e_wallet_name" => $source,
                            "e_wallet_phone_number" => $acc,
                            "mac_address" => "111.111.11.111",
                            "e_wallet_type" => $type,
                            "commission" => $result['Comm'],
                            "fee" => $result['charge'],
                            "api_key" => "IaUJUczIxjIJx6JvSPyfTDcYLvz6B86c"
                        ];

                        $thisrquest = request()->merge($parameters);

                        $maxAttempts = 5;
                        $attempt = 0;
                        $success = 0;

                        while ($attempt < $maxAttempts && $success==0) {
                            $response =  $this->addPaymentInfo($thisrquest);
                            $content = $response->getContent();
                            $txn_for_verify = $result['TxnID'];
                            LaravelLog::info('Direct IFTTT Response txn: '. $txn_for_verify .' try('. $attempt + 1 .') '.$content);

                            if (stripos($content, 'lock') !== false) {
                                $success = 0;
                                sleep(1);
                            }else{
                                $success = 1;

                                if (stripos($content, 'pending') !== false) {

                                    $Txn = Txn::where('txn_no', $txn_for_verify)->orderBy('id', 'DESC')->first();
                                    if($Txn){
                                        $api_key = Api::where('id', $Txn->api_id)->where('type', 'Admin')->first();
                                        if($api_key){
                                            $parameters_for_verify = [
                                                "txn_id" => $txn_for_verify,
                                                "partner_transection_id" => $Txn->partner_transection_id,
                                                "api_key" => $api_key->api_key
                                            ];

                                            $thisrquest_for_verify = request()->merge($parameters_for_verify);
                                            $response_for_verify =  $this->verifyPayment($thisrquest_for_verify);
                                            $content_for_verify = $response_for_verify->getContent();
                                            LaravelLog::info('x Deposit Verify Response txn: '. $txn_for_verify .' response '.$content_for_verify);
                                        }
                                    }
                                }
                            }

                            $attempt++;
                        }
                    }


                    $SmsLognotmatcheds = SmsLog::where('e_wallet_name', $source)->where('e_wallet_no', $acc)->where('matched', 2)->where('sent', 0)->orderBy('id', 'desc')->skip(3)->take(PHP_INT_MAX)->get();
                    foreach($SmsLognotmatcheds as $SmsLognotmatched){
                            if($SmsLognotmatched->type==1){
                                $ttttt_type = "Deposit";
                            }else{
                                $ttttt_type = "Withdrawal";
                            }

                            $formattedDateeeeeeee = Carbon::parse($SmsLognotmatched->created_at)->format('d-m-Y h:i A');
                            $message = "";
                            $message .= "*$source => $acc => $type* \n";
                            $message .= "*Type:* $ttttt_type \n";
                            $message .= "*-------------------------------------* \n";
                            $message .= "Customer: $SmsLognotmatched->customer_acc_no \n";
                            $message .= "TXN: $SmsLognotmatched->txn \n";
                            $message .= "Amount: $SmsLognotmatched->amount \n";
                            $message .= "Comm: $SmsLognotmatched->comm \n";
                            $message .= "Charge: $SmsLognotmatched->charge \n";
                            $message .= "Final Balance: $SmsLognotmatched->final_amount \n";
                            $message .= "DateTime: $formattedDateeeeeeee \n";
                            $message .= "*-------------------------------------* \n";
                            $message .= "*Holded SMS Rejected* \n";

                            $response = Http::post($url, [
                                'chat_id' => $RejectedchatId,
                                'text' => $message,
                                'parse_mode' => 'Markdown',
                            ]);

                            $SmsLognotmatched->sent = 1;
                            $SmsLognotmatched->save();

                        }



                    return 'success';
                }else{
                    LaravelLog::info('Formate note match-xxxxxxxxxxxxxxxx');
                }

            }

           return 'success';
    }

    public function directwebhook(Request $request, $source, $acc, $type){
        $string = file_get_contents('php://input');

        $botToken = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $TestchatId = "-4655286921";
        // $CompletedchatId = "-4754036101";
        // $HoldchatId = "-4735989259";
        // $RejectedchatId = "-4632357788";

        $CompletedchatId = "-1002357517405";
        $HoldchatId = "-1002380966787";
        $RejectedchatId = "-1002488335071";



        LaravelLog::info('Source:'.$source.' Acc:'.$acc.' Type:'.$type.' Message:'.$string);

            // Step 1: Find the position of "Text"
            $textStart = strpos($string, '"content"');

            $result=[];

            if(isset($textStart) && !empty($textStart)){
                $colonPos = strpos($string, ':', $textStart);
                $valueStart = strpos($string, '"', $colonPos + 1) + 1;
                $valueEnd = strpos($string, '",', $valueStart);
                $text = substr($string, $valueStart, $valueEnd - $valueStart);
                $jsonWithoutText = substr($string, 0, $textStart) . substr($string, $valueEnd + 2);
                $jsonWithoutText = rtrim($jsonWithoutText, ",");

                $array = json_decode($jsonWithoutText, true);

                $result = [];


                if($array['from']=="bKash"){

                    $text = preg_replace('/\s*Download.*$/', '', $text);

                    if (strpos($text, "You have received") === 0) {

                        $t_type = 1;

                        // Extract amount after "Tk" and remove commas
                        if (preg_match('/Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract customer phone number after "from" and before "Fee"
                        if (preg_match('/from .*?(\d+)\. (?:Fee|Ref)/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract commission after "Fee" and before "Balance"
                        if (preg_match('/Fee Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['charge'] = 0.00; // Default if not found
                        }

                        // Extract balance after "Balance Tk" and before "TrxID"
                        if (preg_match('/Balance Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['Balance'] = 0.00; // Default if not found
                        }

                        // Extract TrxID after "TrxID" and before "at"
                        if (preg_match('/TrxID (\w+) at/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "at"
                        if (preg_match('/at (.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                        }
                        $result['Comm'] = 0;

                        $result['Comment'] = "You have received";
                    }elseif (strpos($text, "Cash Out") === 0) {

                        if(trim(strtolower($type))=="personal"){
                            $t_type = 2;
                        }else{
                            $t_type = 1;
                        }

                        // Extract amount after "Tk" and remove commas
                        if (preg_match('/Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract customer phone number after "from" and before "Fee"
                        // if (preg_match('/from (.*?) successful/', $text, $matches)) {
                        //     $result['Customer'] = $matches[1];
                        // }

                        if (preg_match('/from (\d{4}[0-9X]{6,10}) successful/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }


                        // Extract commission after "Fee" and before "Balance"
                        if (preg_match('/Fee Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['charge'] = 0.00; // Default if not found
                        }

                        // Extract balance after "Balance Tk" and before "TrxID"
                        if (preg_match('/Balance Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['Balance'] = 0.00; // Default if not found
                        }

                        // Extract TrxID after "TrxID" and before "at"
                        if (preg_match('/TrxID (\w+) at/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "at"
                        if (preg_match('/at (.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                        }

                        $result['Comment'] = "Cash Out";
                        $result['Comm'] = 0;
                    }elseif (strpos($text, "Cash In") === 0) {

                        if(trim(strtolower($type))=="personal"){
                            $t_type = 1;
                        }else{
                            $t_type = 2;
                        }





                        // Extract amount after "Tk" and remove commas
                        if (preg_match('/Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract customer phone number after "from" and before "Fee"
                        // if (preg_match('/from (.*?) successful/', $text, $matches)) {
                        //     $result['Customer'] = $matches[1];
                        // }

                        if (preg_match('/from (\d{4}[0-9X]{6,10}) successful/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }


                        // Extract commission after "Fee" and before "Balance"
                        if (preg_match('/Fee Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['charge'] = 0.00; // Default if not found
                        }

                        // Extract balance after "Balance Tk" and before "TrxID"
                        if (preg_match('/Balance Tk ([\d,]+\.\d+)/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        } else {
                            $result['Balance'] = 0.00; // Default if not found
                        }

                        // Extract TrxID after "TrxID" and before "at"
                        if (preg_match('/TrxID (\w+) at/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "at"
                        if (preg_match('/at (.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                        }

                        $result['Comment'] = "Cash Out";
                        $result['Comm'] = 0;
                    }
                }elseif($array['from']=="NAGAD"){
                    $text = str_replace('\n', "\n", $text);
                    $lines = explode("\n", $text);
                    $lines = array_map('trim', $lines);

                    if(count($lines)>0){
                        $comment = $lines[0];

                        $result = [
                            'Comment' => $comment,
                        ];

                        foreach ($lines as $index => $line) {
                            if ($index === count($lines) - 1) {
                                $result['DateTime'] = trim($line);
                            }elseif (strpos($line, ':') !== false) {
                                [$key, $value] = explode(':', $line, 2);
                                $result[trim($key)] = trim($value);
                            }
                        }
                    }
                    if(isset($result['Sender'])){
                        $result['Customer'] = $result['Sender'];
                    }

                    if(!isset($result['Comm'])){
                        $result['Comm'] = 0;
                    }

                    $result['charge'] = 0;

                    if(isset($result['Receiver'])){
                        $result['Customer'] = $result['Receiver'];
                    }

                    if($result['Comment']=="Cash In Successful." || $result['Comment']=="B2B Transfer Successful."){
                        $t_type = 2;
                    }else{
                        $t_type = 1;
                    }

                }elseif($array['from']=="16216"){


                    if (strpos($text, "B2C: Cash-In") === 0) {

                        $t_type = 2;

                        $text = str_replace('\\', "", $text);

                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }elseif (preg_match('/A\/C:\s*(\*+\d+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Comm/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before "Your"
                        if (preg_match('/Comm:Tk([\d,]+\.\d+);/', $text, $matches)) {
                            $result['Comm'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Comm']) || empty($result['Comm'])){
                            if (preg_match('/Comm:Tk([\d,\.]+)/', $text, $commMatches)) {
                                $result['Comm'] = floatval(str_replace(',', '', $commMatches[1]));
                            }
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance:\s*Tk([\d,]+\.\d+)\s*TxnId/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Balance']) || empty($result['Balance'])){
                            if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                                $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                            }
                        }


                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash In";
                        $result['charge'] = 0;

                    }elseif (strpos($text, "B2C: Cash-Out") === 0) {

                        $t_type = 1;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);

                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }elseif (preg_match('/A\/C:\s*(\*+\d+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Comm/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before ";"
                        if (preg_match('/Comm:Tk([\d,\.]+)/', $text, $commMatches)) {
                            $result['Comm'] = floatval(str_replace(',', '', $commMatches[1]));
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                        }

                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash Out";
                        $result['charge'] = 0;


                    }elseif (str_starts_with($text, "Tk") && strpos($text, "received from") !== false) {



                        $t_type = 1;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);





                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Fee/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*received/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before ";"
                        if (preg_match('/Fee:Tk([\d,\.]+)/', $text, $commMatches)) {
                            $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                        }

                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash Received";
                        $result['Comm'] = 0;

                    }elseif (strpos($text, "Cash-In from") === 0) {



                        $t_type = 1;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);



                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Fee/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before "Your"
                        if (preg_match('/Fee: Tk([\d,]+\.\d+);/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['charge']) || empty($result['charge'])){
                            if (preg_match('/Fee: Tk([\d,\.]+)/', $text, $commMatches)) {
                                $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                            }
                        }

                        if(!isset($result['charge']) || empty($result['charge'])){
                            $result['charge'] = 0;
                        }




                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance:\s*Tk([\d,]+\.\d+)\s*TxnId/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Balance']) || empty($result['Balance'])){
                            if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                                $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                            }
                        }


                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash In";
                        $result['Comm'] = 0;





                    }elseif (strpos($text, "Cash-Out to") === 0) {



                        $t_type = 2;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);
                        $text = preg_replace('/\s*download.*$/', '', $text);



                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Tk/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*Fee/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before "Your"
                        if (preg_match('/Fee:Tk([\d,]+\.\d+);/', $text, $matches)) {
                            $result['charge'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['charge']) || empty($result['charge'])){
                            if (preg_match('/Fee:Tk([\d,\.]+)/', $text, $commMatches)) {
                                $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                            }
                        }



                        if(!isset($result['charge']) || empty($result['charge'])){
                            $result['charge'] = 0;
                        }




                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance:\s*Tk([\d,]+\.\d+)\s*TxnId/', $text, $matches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        if(!isset($result['Balance']) || empty($result['Balance'])){
                            if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                                $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                            }
                        }




                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }




                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = preg_replace('/. Please/i', '', $result['DateTime']);
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash In";
                        $result['Comm'] = 0;





                    }elseif (str_starts_with($text, "Tk") && strpos($text, "transferred to") !== false) {



                        $t_type = 2;

                        $text = str_replace('\\', "", $text);
                        $text = preg_replace('/\s*Download.*$/', '', $text);





                        if (preg_match('/A\/C:\s*([\w\d]+)\s*Fee/', $text, $matches)) {
                            $result['Customer'] = $matches[1];
                        }

                        // Extract Amount after "Tk" and before "Comm"
                        if (preg_match('/Tk([\d,]+\.\d+)\s*transferred/', $text, $matches)) {
                            $result['Amount'] = floatval(str_replace(',', '', $matches[1]));
                        }

                        // Extract Commission after "Comm" and before ";"
                        if (preg_match('/Fee:Tk([\d,\.]+)/', $text, $commMatches)) {
                            $result['charge'] = floatval(str_replace(',', '', $commMatches[1]));
                        }

                        // Extract Balance after "Balance:" and before "TxnId"
                        if (preg_match('/Balance: Tk([\d,\.]+)/', $text, $balanceMatches)) {
                            $result['Balance'] = floatval(str_replace(',', '', $balanceMatches[1]));
                        }

                        // Extract Transaction ID after "TxnId:" and before "Date"
                        if (preg_match('/TxnId:\s*([\d]+)\s*Date/', $text, $matches)) {
                            $result['TxnID'] = $matches[1];
                        }

                        // Extract DateTime after "Date:"
                        if (preg_match('/Date:\s*(.+)$/', $text, $matches)) {
                            $result['DateTime'] = $matches[1];
                            $result['DateTime'] = rtrim($result['DateTime'], '.');
                            $result['DateTime'] = Carbon::createFromFormat('d-M-y h:i:s a', $result['DateTime'])->format('d/m/Y H:i');
                        }

                        $result['Comment'] = "Cash Transferred";
                        $result['Comm'] = 0;

                    }
                }




                if(isset($result['Comment']) && isset($result['Amount']) && isset($result['Customer']) && isset($result['TxnID']) && isset($result['Comm']) && isset($result['Balance']) && isset($result['DateTime'])){
                    $result['Amount'] = preg_replace('/[^0-9.]/', '', $result['Amount']);
                    $result['Balance'] = preg_replace('/[^0-9.]/', '', $result['Balance']);
                    $result['Comm'] = preg_replace('/[^0-9.]/', '', $result['Comm']);
                    $result['charge'] = preg_replace('/[^0-9.]/', '', $result['charge']);
                    $result['Amount'] = floatval($result['Amount']);
                    $result['Balance'] = floatval($result['Balance']);
                    $result['Comm'] = floatval($result['Comm']);
                    $result['charge'] = floatval($result['charge']);


                    $account_balance = 0;
                    DB::beginTransaction();
                    $account = EWalletAccount::where('e_wallet_name', $source)->where('account_no', $acc)->lockForUpdate()->first();
                    if($account){
                        $account_balance = $account->live_balance;
                        if($account->type=="Agent" && $account->e_wallet_name=="bKash"){
                            if($result['Comm']==0){
                                $result['Comm'] = ( $result['Amount'] * 0.4 ) / 100;
                            }
                        }
                    }

                    $account_balance = floatval($account_balance);


                    $final_balance_get = 0;
                    $counter = 0;
                    $recordsmatched = 0;
                    $array_t = [];
                    $SmsLogs = SmsLog::where('e_wallet_name', $source)->where('e_wallet_no', $acc)->orderBy('id', 'desc')->take(3)->get()->sortBy('id');
                    $sumMatched = $SmsLogs->sum('matched');
                    if($sumMatched==6){
                        foreach($SmsLogs as $singleSmsLog){
                            $counter++;
                            if($singleSmsLog->type==1){
                                if($counter==1){
                                    $pre_balance = $singleSmsLog->final_amount - $singleSmsLog->amount - $singleSmsLog->comm + $singleSmsLog->charge;
                                }
                                $total_deposit_n = $pre_balance + $singleSmsLog->amount + $singleSmsLog->comm - $singleSmsLog->charge;
                                if(($singleSmsLog->final_amount - $total_deposit_n < 1) && ($total_deposit_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = $singleSmsLog->charge;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }
                            }else{
                                if($counter==1){
                                    $pre_balance = $singleSmsLog->final_amount + $singleSmsLog->amount - $singleSmsLog->comm + $singleSmsLog->charge;
                                }
                                $total_withdrawal_n = $pre_balance - $singleSmsLog->amount + $singleSmsLog->comm - $singleSmsLog->charge;
                                $total_withdrawal2_n = $pre_balance - $singleSmsLog->amount + $singleSmsLog->comm - 5;
                                $total_withdrawal3_n = $pre_balance - $singleSmsLog->amount + $singleSmsLog->comm - 10;
                                if(($singleSmsLog->final_amount - $total_withdrawal_n < 1) && ($total_withdrawal_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = $singleSmsLog->charge;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }elseif(($singleSmsLog->final_amount - $total_withdrawal2_n < 1) && ($total_withdrawal2_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = 5;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }elseif(($singleSmsLog->final_amount - $total_withdrawal3_n < 1) && ($total_withdrawal3_n - $singleSmsLog->final_amount < 1)){
                                    $array_t[$counter]['customer_acc_no'] = $singleSmsLog->customer_acc_no;
                                    $array_t[$counter]['txn'] = $singleSmsLog->txn;
                                    $array_t[$counter]['amount'] = $singleSmsLog->amount;
                                    $array_t[$counter]['comm'] = $singleSmsLog->comm;
                                    $array_t[$counter]['charge'] = 10;
                                    $array_t[$counter]['id'] = $singleSmsLog->id;
                                    $array_t[$counter]['type'] = $singleSmsLog->type;
                                    $pre_balance = $singleSmsLog->final_amount;
                                    $recordsmatched++;
                                    if($recordsmatched==3){
                                        $final_balance_get = $singleSmsLog->final_amount;
                                    }
                                }
                            }
                        }
                    }

                    if($recordsmatched==3){
                        $account_balance = $final_balance_get;
                    }



                    $total_deposit = $account_balance + $result['Amount'] + $result['Comm'] - $result['charge'];
                    $differance  = $total_deposit - $result['Balance'];

                    $total_withdrawal = $account_balance - $result['Amount'] + $result['Comm'] - $result['charge'];
                    $total_withdrawal2 = $account_balance - $result['Amount'] + $result['Comm'] - 5;
                    $total_withdrawal3 = $account_balance - $result['Amount'] + $result['Comm'] - 10;

                    if($t_type==2){
                        $differance  = $total_withdrawal - $result['Balance'];
                    }

                    LaravelLog::info('SMS_Balance:'.$result['Balance'].' Account_Balance:'.$account_balance.' Differance:'.$differance.' Amount:'.$result['Amount'].' Comm:'.$result['Comm'].' Charge:'.$result['charge']);
                    $saved = 2;
                    if($t_type==1){
                        if(($result['Balance'] - $total_deposit < 1) && ($total_deposit - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Deposit Saved!!!!!!!!!');
                            $saved = 1;
                        }
                    }else{
                        if(($result['Balance'] - $total_withdrawal < 1) && ($total_withdrawal - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Withdrawal Saved!!!!!!!!!');
                            $saved = 1;
                        }elseif(($result['Balance'] - $total_withdrawal2 < 1) && ($total_withdrawal2 - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Withdrawal Saved!!!!!!!!!');
                            $result['charge'] = 5;
                            $saved = 1;
                        }elseif(($result['Balance'] - $total_withdrawal3 < 1) && ($total_withdrawal3 - $result['Balance'] < 1)){
                            if($account){
                                $account->live_balance = $result['Balance'];
                                $account->save();
                            }
                            LaravelLog::info('Withdrawal Saved!!!!!!!!!');
                            $result['charge'] = 10;
                            $saved = 1;
                        }
                    }


                    $Log = new SmsLog();
                    $Log->e_wallet_name = $source;
                    $Log->e_wallet_no = $acc;
                    $Log->customer_acc_no = $result['Customer'];
                    $Log->txn = $result['TxnID'];
                    $Log->account_last_amount = $account_balance;
                    $Log->amount = $result['Amount'];
                    $Log->comm = $result['Comm'];
                    $Log->charge = $result['charge'];
                    $Log->final_amount = $result['Balance'];
                    $Log->type = $t_type;
                    $Log->matched = $saved;
                    $Log->save();


                    DB::commit();



                    $result['DateTime'] = str_replace('\\', "", $result['DateTime']);
                    $date = Carbon::createFromFormat('d/m/Y H:i', $result['DateTime']);
                    $result['DateTime'] = $date->format('h:ia d/m/y');
                    $result['source'] = $source;
                    $result['acc'] = $acc;
                    $result['type'] = $type;



                    if($Log->type==1){
                        $tt_type = "Deposit";
                    }else{
                        $tt_type = "Withdrawal";
                    }

                    if($Log->matched==1){
                        $tt_matched = "Saved";
                        $thischatid = $CompletedchatId;
                    }else{
                        $tt_matched = "On Hold";
                        $thischatid = $HoldchatId;
                    }

                    $formattedDateee = Carbon::parse($Log->created_at)->format('d-m-Y h:i A');

                    $customer_accc = str_replace('*', '⋆', $Log->customer_acc_no);

                    $message = "";
                    $message .= "*$source => $acc => $type* \n";
                    $message .= "*Type:* $tt_type \n";
                    $message .= "*-------------------------------------* \n";
                    $message .= "Customer: $customer_accc \n";
                    $message .= "TXN: $Log->txn \n";
                    $message .= "Amount: $Log->amount \n";
                    $message .= "Comm: $Log->comm \n";
                    $message .= "Charge: $Log->charge \n";
                    $message .= "Final Balance: $Log->final_amount \n";
                    $message .= "DateTime: $formattedDateee \n";
                    $message .= "*-------------------------------------* \n";
                    $message .= "*$tt_matched* \n";

                    $response = Http::post($url, [
                        'chat_id' => $thischatid,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                    ]);




                    if($saved == 2){
                        LaravelLog::info('Balance not match-xxxxxxxxxxxxxxxx');

                    }else{
                        if($recordsmatched==3){
                            foreach($array_t as $array_t_o){
                                $logid = $array_t_o['id'];
                                $SmsLogsingle = SmsLog::where('id', $logid)->first();
                                if($array_t_o['type']==1){
                                    $ttt_type = "Deposit";
                                    $SmsLogsingle->charge = $array_t_o['charge'];
                                    $SmsLogsingle->matched = 1;
                                    $SmsLogsingle->save();
                                    LaravelLog::info('x Deposit Saved txn: '.$array_t_o['txn']);
                                    $parameters = [
                                        "sender" => $array_t_o['customer_acc_no'],
                                        "txn_id" => $array_t_o['txn'],
                                        "amount" => $array_t_o['amount'],
                                        "date" => $result['DateTime'],
                                        "time" => $result['DateTime'],
                                        "transaction_type" => "Payment IN",
                                        "e_wallet_name" => $source,
                                        "e_wallet_phone_number" => $acc,
                                        "mac_address" => "111.111.11.111",
                                        "e_wallet_type" => $type,
                                        "commission" => $array_t_o['comm'],
                                        "fee" => $array_t_o['charge'],
                                        "api_key" => "IaUJUczIxjIJx6JvSPyfTDcYLvz6B86c"
                                    ];

                                    $thisrquest = request()->merge($parameters);

                                    $maxAttempts = 5;
                                    $attempt = 0;
                                    $success = 0;

                                    while ($attempt < $maxAttempts && $success==0) {
                                        $response =  $this->addPaymentInfo($thisrquest);
                                        $content = $response->getContent();
                                        $txn_for_verify = $array_t_o['txn'];
                                        LaravelLog::info('x Deposit Response txn: '. $txn_for_verify .' try('. $attempt + 1 .') '.$content);

                                        if (stripos($content, 'lock') !== false) {
                                            $success = 0;
                                            sleep(1);
                                        }else{
                                            $success = 1;

                                            if (stripos($content, 'pending') !== false) {
                                               $Txn = Txn::where('txn_no', $txn_for_verify)->orderBy('id', 'DESC')->first();
                                                if($Txn){
                                                    $api_key = Api::where('id', $Txn->api_id)->where('type', 'Admin')->first();
                                                    if($api_key){
                                                        $parameters_for_verify = [
                                                            "txn_id" => $txn_for_verify,
                                                            "partner_transection_id" => $Txn->partner_transection_id,
                                                            "api_key" => $api_key->api_key
                                                        ];

                                                        $thisrquest_for_verify = request()->merge($parameters_for_verify);
                                                        $response_for_verify =  $this->verifyPayment($thisrquest_for_verify);
                                                        $content_for_verify = $response_for_verify->getContent();
                                                        LaravelLog::info('x Deposit Verify Response txn: '. $txn_for_verify .' response '.$content_for_verify);
                                                    }
                                                }
                                            }



                                        }

                                        $attempt++;
                                    }




                                }else{
                                    $ttt_type = "Withdrawal";
                                    LaravelLog::info('x Withdrawal Saved txn: '.$array_t_o['txn']);
                                }

                                $formattedDateeee = Carbon::parse($SmsLogsingle->created_at)->format('d-m-Y h:i A');

                                $customer_acccc = str_replace('*', '⋆', $SmsLogsingle->customer_acc_no);

                                $message = "";
                                $message .= "*$source => $acc => $type* \n";
                                $message .= "*Type:* $ttt_type \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "Customer: $customer_acccc \n";
                                $message .= "TXN: $SmsLogsingle->txn \n";
                                $message .= "Amount: $SmsLogsingle->amount \n";
                                $message .= "Comm: $SmsLogsingle->comm \n";
                                $message .= "Charge: $SmsLogsingle->charge \n";
                                $message .= "Final Balance: $SmsLogsingle->final_amount \n";
                                $message .= "DateTime: $formattedDateeee \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "*Holded SMS Saved* \n";

                                $response = Http::post($url, [
                                    'chat_id' => $CompletedchatId,
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                ]);

                            }
                        }

                    }








                    $SmsLog = SmsLog::where('e_wallet_name', $source)->where('e_wallet_no', $acc)->where('matched', 2)->orderBy('id', 'desc')->first();
                    if($SmsLog){
                        if($SmsLog->type==1){
                            $previous_total_deposit = $result['Balance'] + $SmsLog->amount + $SmsLog->comm - $SmsLog->charge;
                            if(($SmsLog->final_amount - $previous_total_deposit < 1) && ($previous_total_deposit - $SmsLog->final_amount < 1)){
                                $SmsLog->matched = 1;
                                $SmsLog->save();

                                LaravelLog::info('Previous Deposit Saved!!!!!!!!!');

                                DB::beginTransaction();
                                $accountt = EWalletAccount::where('e_wallet_name', $source)->where('account_no', $acc)->lockForUpdate()->first();
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $accountt->save();
                                }
                                DB::commit();

                                $parameters = [
                                    "sender" => $SmsLog->customer_acc_no,
                                    "txn_id" => $SmsLog->txn,
                                    "amount" => $SmsLog->amount,
                                    "date" => $result['DateTime'],
                                    "time" => $result['DateTime'],
                                    "transaction_type" => $result['Comment'],
                                    "e_wallet_name" => $source,
                                    "e_wallet_phone_number" => $acc,
                                    "mac_address" => "111.111.11.111",
                                    "e_wallet_type" => $type,
                                    "commission" => $SmsLog->comm,
                                    "fee" => $SmsLog->charge,
                                    "api_key" => "IaUJUczIxjIJx6JvSPyfTDcYLvz6B86c"
                                ];

                                $thisrquest = request()->merge($parameters);

                                $maxAttempts = 5;
                                $attempt = 0;
                                $success = 0;

                                while ($attempt < $maxAttempts && $success==0) {
                                    $response =  $this->addPaymentInfo($thisrquest);
                                    $content = $response->getContent();
                                    $txn_for_verify = $SmsLog->txn;
                                    LaravelLog::info('Direct IFTTT Response Previous txn: '. $txn_for_verify .' try('. $attempt + 1 .') '.$content);

                                    if (stripos($content, 'lock') !== false) {
                                        $success = 0;
                                        sleep(1);
                                    }else{
                                        $success = 1;

                                        if (stripos($content, 'pending') !== false) {
                                            $Txn = Txn::where('txn_no', $txn_for_verify)->orderBy('id', 'DESC')->first();
                                            if($Txn){
                                                $api_key = Api::where('id', $Txn->api_id)->where('type', 'Admin')->first();
                                                if($api_key){
                                                    $parameters_for_verify = [
                                                        "txn_id" => $txn_for_verify,
                                                        "partner_transection_id" => $Txn->partner_transection_id,
                                                        "api_key" => $api_key->api_key
                                                    ];

                                                    $thisrquest_for_verify = request()->merge($parameters_for_verify);
                                                    $response_for_verify =  $this->verifyPayment($thisrquest_for_verify);
                                                    $content_for_verify = $response_for_verify->getContent();
                                                    LaravelLog::info('x Deposit Verify Response txn: '. $txn_for_verify .' response '.$content_for_verify);
                                                }
                                            }
                                        }
                                    }

                                    $attempt++;
                                }


                                $formattedDateeeee = Carbon::parse($SmsLog->created_at)->format('d-m-Y h:i A');

                                $customer_accccc = str_replace('*', '⋆', $SmsLog->customer_acc_no);

                                $message = "";
                                $message .= "*$source => $acc => $type* \n";
                                $message .= "*Type:* Deposit \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "Customer: $customer_accccc \n";
                                $message .= "TXN: $SmsLog->txn \n";
                                $message .= "Amount: $SmsLog->amount \n";
                                $message .= "Comm: $SmsLog->comm \n";
                                $message .= "Charge: $SmsLog->charge \n";
                                $message .= "Final Balance: $SmsLog->final_amount \n";
                                $message .= "DateTime: $formattedDateeeee \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "*Holded SMS Saved* \n";

                                $response = Http::post($url, [
                                    'chat_id' => $CompletedchatId,
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                ]);
                            }
                        }else{


                            $previous_withdrawal = $result['Balance'] - $SmsLog->amount + $SmsLog->comm - $SmsLog->charge;
                            $previous_withdrawal2 = $result['Balance'] - $SmsLog->amount + $SmsLog->comm - 5;
                            $previous_withdrawal3 = $result['Balance'] - $SmsLog->amount + $SmsLog->comm - 10;

                            DB::beginTransaction();
                            $accountt = EWalletAccount::where('e_wallet_name', $source)->where('account_no', $acc)->lockForUpdate()->first();
                            $p_matched = 2;
                            if(($SmsLog->final_amount - $previous_withdrawal < 1) && ($previous_withdrawal - $SmsLog->final_amount < 1)){
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $accountt->save();
                                }
                                $p_matched = 1;
                                LaravelLog::info('Previous Withdrawal Saved!!!!!!!!!');
                            }elseif(($SmsLog->final_amount - $previous_withdrawal2 < 1) && ($previous_withdrawal2 - $SmsLog->final_amount < 1)){
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $SmsLog->charge = 5;
                                    $accountt->save();
                                }
                                $p_matched = 1;
                                LaravelLog::info('Previous Withdrawal Saved!!!!!!!!!');

                            }elseif(($SmsLog->final_amount - $previous_withdrawal3 < 1) && ($previous_withdrawal3 - $SmsLog->final_amount < 1)){
                                if($accountt){
                                    $accountt->live_balance = $SmsLog->final_amount;
                                    $SmsLog->charge = 10;
                                    $accountt->save();
                                }
                                $p_matched = 1;
                                LaravelLog::info('Previous Withdrawal Saved!!!!!!!!!');
                            }

                            DB::commit();


                            $SmsLog->matched = $p_matched;
                            $SmsLog->save();

                            if($p_matched==1){
                                $formattedDateeeeee = Carbon::parse($SmsLog->created_at)->format('d-m-Y h:i A');

                                $customer_acccccc = str_replace('*', '⋆', $SmsLog->customer_acc_no);

                                $message = "";
                                $message .= "*$source => $acc => $type* \n";
                                $message .= "*Type:* Withdrawal \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "Customer: $customer_acccccc \n";
                                $message .= "TXN: $SmsLog->txn \n";
                                $message .= "Amount: $SmsLog->amount \n";
                                $message .= "Comm: $SmsLog->comm \n";
                                $message .= "Charge: $SmsLog->charge \n";
                                $message .= "Final Balance: $SmsLog->final_amount \n";
                                $message .= "DateTime: $formattedDateeeeee \n";
                                $message .= "*-------------------------------------* \n";
                                $message .= "*Holded SMS Saved* \n";

                                $response = Http::post($url, [
                                    'chat_id' => $CompletedchatId,
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                ]);
                            }






                        }

                    }




                    if(($result['Balance'] - $total_deposit < 1) && ($total_deposit - $result['Balance'] < 1)){
                        $parameters = [
                            "sender" => $result['Customer'],
                            "txn_id" => $result['TxnID'],
                            "amount" => $result['Amount'],
                            "date" => $result['DateTime'],
                            "time" => $result['DateTime'],
                            "transaction_type" => $result['Comment'],
                            "e_wallet_name" => $source,
                            "e_wallet_phone_number" => $acc,
                            "mac_address" => "111.111.11.111",
                            "e_wallet_type" => $type,
                            "commission" => $result['Comm'],
                            "fee" => $result['charge'],
                            "api_key" => "IaUJUczIxjIJx6JvSPyfTDcYLvz6B86c"
                        ];

                        $thisrquest = request()->merge($parameters);

                        $maxAttempts = 5;
                        $attempt = 0;
                        $success = 0;

                        while ($attempt < $maxAttempts && $success==0) {
                            $response =  $this->addPaymentInfo($thisrquest);
                            $content = $response->getContent();
                            $txn_for_verify = $result['TxnID'];
                            LaravelLog::info('Direct IFTTT Response txn: '. $txn_for_verify .' try('. $attempt + 1 .') '.$content);

                            if (stripos($content, 'lock') !== false) {
                                $success = 0;
                                sleep(1);
                            }else{
                                $success = 1;

                                if (stripos($content, 'pending') !== false) {

                                    $Txn = Txn::where('txn_no', $txn_for_verify)->orderBy('id', 'DESC')->first();
                                    if($Txn){
                                        $api_key = Api::where('id', $Txn->api_id)->where('type', 'Admin')->first();
                                        if($api_key){
                                            $parameters_for_verify = [
                                                "txn_id" => $txn_for_verify,
                                                "partner_transection_id" => $Txn->partner_transection_id,
                                                "api_key" => $api_key->api_key
                                            ];

                                            $thisrquest_for_verify = request()->merge($parameters_for_verify);
                                            $response_for_verify =  $this->verifyPayment($thisrquest_for_verify);
                                            $content_for_verify = $response_for_verify->getContent();
                                            LaravelLog::info('x Deposit Verify Response txn: '. $txn_for_verify .' response '.$content_for_verify);
                                        }
                                    }
                                }
                            }

                            $attempt++;
                        }
                    }


                    $SmsLognotmatcheds = SmsLog::where('e_wallet_name', $source)->where('e_wallet_no', $acc)->where('matched', 2)->where('sent', 0)->orderBy('id', 'desc')->skip(3)->take(PHP_INT_MAX)->get();
                    foreach($SmsLognotmatcheds as $SmsLognotmatched){
                            if($SmsLognotmatched->type==1){
                                $ttttt_type = "Deposit";
                            }else{
                                $ttttt_type = "Withdrawal";
                            }

                            $formattedDateeeeeeee = Carbon::parse($SmsLognotmatched->created_at)->format('d-m-Y h:i A');

                            $customer_accccccc = str_replace('*', '⋆', $SmsLognotmatched->customer_acc_no);


                            $message = "";
                            $message .= "*$source => $acc => $type* \n";
                            $message .= "*Type:* $ttttt_type \n";
                            $message .= "*-------------------------------------* \n";
                            $message .= "Customer: $customer_accccccc \n";
                            $message .= "TXN: $SmsLognotmatched->txn \n";
                            $message .= "Amount: $SmsLognotmatched->amount \n";
                            $message .= "Comm: $SmsLognotmatched->comm \n";
                            $message .= "Charge: $SmsLognotmatched->charge \n";
                            $message .= "Final Balance: $SmsLognotmatched->final_amount \n";
                            $message .= "DateTime: $formattedDateeeeeeee \n";
                            $message .= "*-------------------------------------* \n";
                            $message .= "*Holded SMS Rejected* \n";

                            $response = Http::post($url, [
                                'chat_id' => $RejectedchatId,
                                'text' => $message,
                                'parse_mode' => 'Markdown',
                            ]);

                            $SmsLognotmatched->sent = 1;
                            $SmsLognotmatched->save();

                        }



                    return 'success';
                }else{
                    LaravelLog::info('Formate note match-xxxxxxxxxxxxxxxx');
                }

            }

           return 'success';
    }



}
