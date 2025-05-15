<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api;
use App\Models\Log;
use App\Models\Payout;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Signature;
use App\Models\EWalletAccount;
use Illuminate\Support\Facades\Log as LaravelLog;
use App\Models\Commission;
use App\Models\DailyPartnerSummaryLog;
use App\Models\DailyPartnerSummary;
use App\ModelsPartnerCommission;
use Illuminate\Support\Facades\Validator;
use App\Models\Txn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Stevebauman\Purify\Facades\Purify;

class PaymentLogController extends Controller
{

    public function index()
    {
        $pageTitle = "Payment Logs";
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway','txn_record')->paginate(config('basic.paginate'));
        return view('admin.payment.logs', compact('funds', 'pageTitle', 'domains'));
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
        $pageTitle = "Payment Report";
        $domains = Api::where('type', 'Admin')->get();
        $funds = Payment::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));
        $funds_t = Payment::where('status', '!=', 'initiate')->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);
        return view('admin.payment.report', compact('funds', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum'));
    }

    public function reportSearch(Request $request)
    {
        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)->get();
        $search = $request->all();
        $fund_count = 0;
        $fund_sum = 0;
        $funds = Payment::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));

        // Aggregate totals (COUNT & SUM)
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
            ->when($search['status'] != 4, function ($query) use ($search) {
                $query->where('status', $search['status']);
            })
            ->when($search['website'], function ($query) use ($search) {
                $query->where('api_id', $search['website']);
            })
            ->where(function ($query) use ($request) {
                $query->where('sender', 'LIKE', "%{$request->account_no}%")
                      ->where('e_wallet_name', 'LIKE', "%{$request->gateway}%");
            })
            ->select(DB::raw('COUNT(*) as amount_count, SUM(amount) as amount_sum'))
            ->first();

        $fund_count = $funds_t->amount_count ?? 0;
        $fund_sum = round($funds_t->amount_sum ?? 0, 2);

        // Paginated list of payments
        $payments = Payment::where('status', '!=', 0)
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
                    $subQuery->where('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('transaction', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%')
                        ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                });
            })
            ->when($search['status'] != 4, function ($query) use ($search) {
                $query->where('status', $search['status']);
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
            ->with('user', 'gateway')
            ->paginate(config('basic.paginate'));

        $pageTitle = "Search Payment Logs";
        return view('admin.payment.report', compact('funds','payments', 'pageTitle', 'gateways', 'fund_count', 'fund_sum', 'domains'));
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
            DB::raw('COUNT(CASE WHEN status = 2 THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = 1 THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as complete_amount')
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
            DB::raw('COUNT(CASE WHEN status = 2 THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = 1 THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as complete_amount')
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
            $status = 2;
        } elseif ($status == "Approved") {
            $status = 1;
        } else {
            $status = "";
        }



        $funds = Payment::where('status', '!=', 'initiate')
            ->orderBy('id', 'DESC')
            ->with('user', 'gateway')
            ->whereDate('created_at', $date)
            ->whereHas('payment', function ($query) use ($date, $gateway) {
                $query->where('e_wallet_name', 'like', '%' . $gateway . '%'); // Add the e_wallet_name condition
            })
            ->when($status != -1, function ($query) use ($status) {
                return $query->where('status', 'like', '%' . $status . '%');
            })
            ->paginate(config('basic.paginate'));

            $funds_t = Payment::where('status', '!=', 'initiate')
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
            ->with('user', 'gateway')
            ->whereDate('created_at', $date)
            ->where('e_wallet_name', 'like', '%' . $gateway . '%') // Moved this condition here
            ->when($status != -1, function ($query) use ($status) {
                return $query->where('status', 'like', '%' . $status . '%');
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
            'status' => ['required', Rule::in(['1', '3'])],
        ]);

        DB::beginTransaction();
        try {
            $data = Payment::where('id', $request->id)->whereIn('status', ['Complete'])->lockForUpdate()->with('user', 'gateway')->firstOrFail();
            if (!empty($request->sender)) {
                $data->account_no = $request->sender;
                $data->save();
            }

            $basic = (object)config('basic');
            $req = Purify::clean($request->all());
            $commit = 0;

            if ($request->status == '1') {

                $account = EWalletAccount::where('e_wallet_name', $data->gateway->code)
                    ->where('account_no', $request->e_wallet_phone_number)
                    ->where('status', 'Pending')
                    ->first();
                if (!$account) {
                    throw new \Exception("E-Wallet Account Disable or not Exist.");
                }

                $formattedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->date_time)->format('Y-m-d');
                $formattedTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->date_time)->format('H:i:s');
                $formattedDateTime = Carbon::parse($request->date_time)->format('Y-m-d H:i:s');

                $new = 0;
                if (empty($request->txn_id)) {
                    $request->txn_id = "none";
                    $payment = Payment::where('e_wallet_name', $data->gateway->code)
                        ->where('amount', $data->amount)
                        ->where('sender', $data->account_no)
                        ->whereDate('date', '=', $formattedDate)
                        ->where('status', 'Pending')
                        ->orderBy('id', 'DESC')
                        ->first();
                } else {
                    $check_payment = Payment::where('txn_id', $request->txn_id)
                        ->where('status', 'Complete')
                        ->first();
                    if ($check_payment) {
                        throw new \Exception("By This Txn no, Payment Already Completed.");
                    }

                    $payment = Payment::where('txn_id', $request->txn_id)->where('status', 'Pending')->orderBy('id', 'DESC')->first();
                    if ($payment) {
                        if ($payment->amount != $data->amount) {
                            throw new \Exception("Wrong TXN.");
                        }
                    }
                }

                if (!$payment) {
                    $payment = new Payment();
                    $new = 1;
                }

                if ($new == 1) {
                    $payment->date = $formattedDate;
                    $payment->time = $formattedTime;
                    $payment->date_time = $formattedDateTime;
                }


                $source = "";
                $charge = 0;
                $api_id = "";

                $partner_api_key = Api::where('id', $data->api_id)->lockForUpdate()->first();
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

                        $commissions = Commission::where('api_id', $partner_api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
                        if ($commissions) {
                            $charge = $commissions->deposit_percentage * $data->amount / 100;
                        } else {
                            $commissions = Commission::where('api_id', $partner_api_key->id)->orderBy('to_amount', 'desc')->first();
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

                $payment->e_wallet_name = $data->gateway->code;
                $payment->amount = $data->amount;
                $payment->sender = $data->account_no;
                $payment->txn_id = $request->txn_id;
                $payment->transaction_type = 'Received Money';
                $payment->e_wallet_phone_number = $request->e_wallet_phone_number;
                $payment->e_wallet_type = $request->e_wallet_type;
                $payment->source = $source;
                $payment->api_id = $api_id;
                $payment->charge = $charge;
                $payment->status = 'Complete';
                $payment->completed_source = 'AdminPanel';
                $payment->created_at = $data->created_at;
                $payment->trans_complete_date = Carbon::now();
                $payment->transaction_id = $data->id;
                $payment->partner_transection_id = $data->partner_transection_id;
                $payment->member_id = $data->member_id;
                $payment->save();

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
                    $summary_log->payment_id = $payment->id;
                    $summary_log->total_amount = $net_amount;
                    $summary_log->summary_id = $DailyPartnerSummary_record->id;
                    $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                    $summary_log->source = 'AdminPanel';
                    $summary_log->save();
                }

                if ($new == 1) {
                    $e_wallet_charge = 0;
                    $count_payments = Payment::where('e_wallet_name', $data->gateway->code)->where('status', 'Complete')->where('e_wallet_phone_number', $request->e_wallet_phone_number)->whereDate('date', $formattedDate)->count();
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
                    $Log->transection_id = $payment->id;
                    $Log->partner_id = $partner_api_key->id;
                    $Log->source = 'AdminPanel';
                    $Log->save();
                }

                $this->updateLimits();
                $account = EWalletAccount::where('e_wallet_name', $data->gateway->code)
                    ->where('account_no', $request->e_wallet_phone_number)
                    ->where('status', 1)
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
                    $e_wallet_log_save->transaction_id = $payment->id;
                    $e_wallet_log_save->account_id = $account->id;
                    $e_wallet_log_save->source = 'action';
                    $e_wallet_log_save->save();
                }

                $data->status = 1;
                $data->feedback = @$req['feedback'];
                $data->payment_id = $payment->id;
                $data->created_at = $data->created_at;
                $data->trans_completed_date = Carbon::now();
                $data->update();

                $PartnerCommissions = PartnerCommission::where('transaction_id', $data->id)->where('type', 1)->where('status', 0)->get();
                foreach ($PartnerCommissions as $PartnerCommission) {
                    $PartnerCommission->status = 1;
                    $PartnerCommission->save();
                    $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
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

                $user = $data->user;
                $user->balance += $data->amount;
                $user->save();

                $commit = 1;
                DB::commit();

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
                            'completion_date' => $payment->date,
                            'completion_time' => $payment->time,
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

                $remarks = getAmount($data->amount) . ' ' . $basic->currency . ' payment amount has been approved';
                BasicService::makeTransaction($user, getAmount($data->amount), getAmount($data->charge),  '+', $data->transaction, $remarks);

                if ($basic->deposit_commission == 1) {
                    BasicService::setBonus($user, getAmount($data->amount), 'deposit');
                }

                $msg = [
                    'amount' => getAmount($data->amount),
                    'currency' => $basic->currency,
                ];
                $action = [
                    "link" => '#',
                    "icon" => "fas fa-money-bill-alt text-white"
                ];
                // $this->userPushNotification($user, 'PAYMENT_APPROVED', $msg, $action);
                session()->flash('success', 'Approve Successfully');
            } elseif ($request->status == '3') {

                $data->status = 3;
                $data->feedback = $request->feedback;
                $data->update();
                $user = $data->user;

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


                $msg = [
                    'amount' => getAmount($data->amount),
                    'currency' => $basic->currency,
                    'feedback' => $data->feedback,
                ];
                $action = [
                    "link" => '#',
                    "icon" => "fas fa-money-bill-alt text-white"
                ];
                // $this->userPushNotification($user, 'PAYMENT_REJECTED', $msg, $action);
                session()->flash('success', 'Reject Successfully');

            }
            if($commit==0){
                DB::commit();
            }
            dd('hello');
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
                $data->e_wallet_phone_number = $request->e_wallet_phone_number;
                $data->save();


                if ($data) {
                    $pre_e_wallet_phone_number = $data->e_wallet_phone_number;
                    $data->e_wallet_phone_number = $request->e_wallet_phone_number;
                    $data->save();

                    $account = EWalletAccount::where('e_wallet_name', $data->e_wallet_name)
                        ->where('account_no', $pre_e_wallet_phone_number)
                        ->where('status', 1)
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
                        ->where('status', 1)
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
                                'completion_date' => $payment->date,
                                'completion_time' => $payment->time,
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
            $funds = Payment::where('status', '!=', 0)
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
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    })->orWhereHas('payment', function ($subQuery) use ($search) {
                        $subQuery->where('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when($search['status'] != 4, function ($query) use ($search) {
                    if ($search['status'] == 99) {
                        // Get records where status is 2 (pending) and created more than 10 minutes ago
                        $query->where('status', 2)
                              ->where('created_at', '<', Carbon::now()->subMinutes(10));
                    } else if ($search['status'] == 2) {
                        // Get records where status is 2 (pending) and created within the last 10 minutes
                        $query->where('status', 2)
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
                $status = "Pending";
                if ($fund->status == 2) {
                    $status = "Pending";
                } elseif ($fund->status == 1) {
                    $status = "Completed";
                } elseif ($fund->status == 3) {
                    $status = "Rejected";
                }

                $data[] = [$fund->created_at, $fund->transaction, optional($fund->payment)->txn_id, $partner_transection_id, $user_name, $user_type, optional($fund->gateway)->name, $fund->account_no, getAmount($fund->amount), getAmount($fund->charge), getAmount($fund->final_amount), $status, $fund->e_wallet_phone_number, optional($fund->api)->website, $fund->source, optional($fund->payment)->updated_at];
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

            $funds = Payment::where('status', '!=', 0)
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
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    })->orWhereHas('payment', function ($subQuery) use ($search) {
                        $subQuery->where('txn_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                  ->when($search['status'] != 4, function ($query) use ($search) {
                    if ($search['status'] == 99) {
                        // Get records where status is 2 (pending) and created more than 10 minutes ago
                        $query->where('status', 2)
                              ->where('created_at', '<', Carbon::now()->subMinutes(10));
                    } else if ($search['status'] == 2) {
                        // Get records where status is 2 (pending) and created within the last 10 minutes
                        $query->where('status', 2)
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
        $maxAttempts = 3;
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

                $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->first();
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
                $payment_record = Payment::where('txn_id', $request->txn_id)->orderBy('id', 'DESC')->lockForUpdate()->first();
                if (!$payment_record) {
                    return response()->json(['message' => 'Please Wait! Your Payment is Processing.']);
                }

                if ($payment_record->status == "Complete") {
                    return response()->json(['message' => 'With This Transaction No. Payment Already Completed.']);
                }

                $currentMonth = now()->format('Y-m');
                $now = Carbon::now();
                $twoHoursAgo = $now->subHours(2);

                $charge = 0;

                $order = Payment::where('partner_transection_id', $partner_transection_id)->where('amount', $payment_record->amount)->where('api_id', $api_id)->whereIn('status', [0, 2])->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->with(['gateway', 'user'])->lockForUpdate()->first();
                if (!$order) {
                    if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        $order = Payment::where(function ($query) use ($payment_record) {
                            $query->where('sender', 'LIKE', substr($payment_record->sender, 0, 4) . '%')
                                ->where('sender', 'LIKE', '%' . substr($payment_record->sender, -3));
                        })->where('amount', $payment_record->amount)->where('api_id', $api_id)->whereIn('status', [0, 2])->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->with(['gateway', 'user'])->lockForUpdate()->first();
                        if($order){
                            $payment_record->sender = $order->sender;
                        }
                    }elseif (strpos($payment_record->sender, '***') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        $order = Payment::where('sender', 'LIKE', '%' . substr($payment_record->sender, -3))->where('amount', $payment_record->amount)->where('api_id', $api_id)->whereIn('status', [0, 2])->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->with(['gateway', 'user'])->lockForUpdate()->first();
                        if($order){
                            $payment_record->sender = $order->sender;
                        }
                    }else{
                        $order = Payment::where('sender', $payment_record->sender)->where('amount', $payment_record->amount)->where('api_id', $api_id)->whereIn('status', [0, 2])->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->with(['gateway', 'user'])->lockForUpdate()->first();
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

                        $commissions = Commission::where('api_id', $partner_api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
                        if ($commissions) {
                            $charge = $commissions->deposit_percentage * $payment_record->amount / 100;
                        } else {
                            $commissions = Commission::where('api_id', $partner_api_key->id)->orderBy('to_amount', 'desc')->first();
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
                        $Log->transection_id = $payment_record->id;
                        $Log->partner_id = $api_balance_row->id;
                        $Log->source = 'APIVerify';
                        $Log->save();
                    }

                    $payment_record->status = 'Complete';
                    $order->status = 1;
                    $order->created_at = $order->created_at;
                    $order->trans_completed_date = Carbon::now();
                    $payment_record->created_at = $order->created_at;
                    $payment_record->trans_complete_date = Carbon::now();
                    $payment_record->completed_source = 'APIVerify';

                    $payment_record->transaction_id = $order->id;
                    $payment_record->api_id = $api_id;
                    $payment_record->source = $source;
                    $payment_record->partner_transection_id = $order->partner_transection_id;
                    $payment_record->member_id = $order->member_id;
                    $payment_record->charge = $charge;
                    $payment_record->save();
                    $order->sender = $payment_record->sender;
                    $order->payment_id = $payment_record->id;
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
                        $summary_log->payment_id = $payment_record->id;
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

                    if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                        $string_to_hash = json_encode(array(
                            "amount" => strval($this->convertStringToNumber($payment_record->amount)),
                            "api_key" => $partner_api_key->api_key,
                            "e_wallet_name" => $payment_record->e_wallet_name,
                            "id" => strval($payment_record->id),
                            'transaction_type' => 'Deposit',
                            "user_sender" => strval($payment_record->sender),

                        ));
                        $secretKey = $partner_api_key->secret_key;
                        $hash = hash("sha256", $string_to_hash);
                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                        $timestamp = time();
                        $combined = $hmac . $timestamp;
                        $sign = base64_encode($combined);


                        $array_data = [
                                    'id' => $payment_record->id,
                                    'partner_transection_id' => $payment_record->partner_transection_id,
                                    'transaction_type' => 'Deposit',
                                    'e_wallet_name' => $payment_record->e_wallet_name,
                                    'amount' => $this->convertStringToNumber($payment_record->amount),
                                    'user_sender' => $payment_record->sender,
                                    'txn_id' => $payment_record->txn_id,
                                    'e_wallet_phone_number' => $payment_record->e_wallet_phone_number,
                                    'e_wallet_type' => $payment_record->e_wallet_type,
                                    'charges' => $this->convertStringToNumber($payment_record->charge),
                                    'status' => $payment_record->status,
                                    'completion_date' => $payment_record->date,
                                    'completion_time' => $payment_record->time,
                                    'created_at' => $payment_record->created_at,
                                    'updated_at' => $payment_record->updated_at,
                                    'sign' => $sign,
                        ];

                        if(!empty($payment_record->member_id)){
                            $array_data['member_id'] = $payment_record->member_id;
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
}
