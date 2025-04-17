<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api;
use App\Models\ApiHit;
use App\Models\Commission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EWalletAccount;
use App\Models\ApiTransaction;
use App\Models\Transaction;
use App\Models\CronCommission;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\EWalletLog;
use App\Models\AccountLog;
use App\Models\EWalletCharge;
use App\Models\Log;
use App\Models\DailyPartnerSummary;
use App\Models\DailyPartnerSummaryLog;
use Carbon\Carbon;
// rehan

use App\Models\Settlement;
use App\Http\Traits\Upload;
use App\Models\SmsLog;
use App\Models\EWalletTransfer;
use App\Models\Gateway;
use Illuminate\Support\Facades\Http;

class PayoutRecordController extends Controller
{
    use Upload;

    public function reportDetail($date, $gateway, $status)
    {

        $gateways = Gateway::where('status', 1)
            ->get();
        // dd($gateways);
        $pageTitle = "Payout Report Detail";
        $domains = Api::where('type', 'Admin')->get();

        $heading['date'] = $date;
        $heading['gateway'] = $gateway;
        $heading['status'] = $status;

        if ($gateway == "All") {
            $gateway = "";
        }

        if ($status == "Pending") {
            $status = 1;
        } elseif ($status == "Approved") {
            $status = 2;
        } else {
            $status = "";
        }

        // dd($status);

        $records = Payout::where('status', 'like', '%' . $status . '%')
    ->where('status', '!=', 0)
    ->whereDate('created_at', $date) // Moved here directly
    ->where('e_wallet_name', 'like', '%' . $gateway . '%') // Moved here directly
    ->orderBy('id', 'DESC')
    ->with('user', 'method')
    ->paginate(config('basic.paginate'));


    $funds_t = PayoutLog::where('status', '!=', 0)
    ->where('status', 'like', '%' . $status . '%')
    ->whereDate('created_at', $date) // Applied directly
    ->where('e_wallet_name', 'like', '%' . $gateway . '%') // Applied directly
    ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
    ->with('user', 'method') // Removed 'payout'
    ->first();

        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);

        return view('admin.payout.report_detail', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum', 'heading'));
    }

    public function report()
    {
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Payout Report";
        $domains = Api::where('type', 'Admin')->get();
        $records = Payout::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));
        $funds_t = Payout::where('status', '!=', 'initiate')->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);
        return view('admin.payout.report', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum'));
    }

    public function reportSearch(Request $request)
    {
        $search = $request->all();
        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)->get();

        // Clone base query for count & sum separately
        $baseQuery = Payout::query()
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', $search['name'])
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%{$search['name']}%")
                            ->orWhere('username', 'LIKE', "%{$search['name']}%");
                    });
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                if ($search['status'] == 2) {
                    return $query->where('status', 2)->where('status', 'Complete');
                } else {
                    return $query->where('status', $search['status']);
                }
            })
            ->when(isset($search['domain']), function ($query) use ($search) {
                return $query->where('api_id', $search['domain']);
            })
            ->where('status', '!=', 0)
            ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                return $query->whereDate('created_at', '>=', $search['from_date'])
                             ->whereDate('created_at', '<=', $search['to_date']);
            })
            ->when(isset($search['account_no']), function ($query) use ($search) {
                return $query->where('user_account_no', 'LIKE', "%{$search['account_no']}%");
            })
            ->when(isset($search['gateway']), function ($query) use ($search) {
                return $query->where('e_wallet_name', 'LIKE', "%{$search['gateway']}%");
            });

        // Get totals separately
        $fund_count = (clone $baseQuery)->count();
        $fund_sum = round((clone $baseQuery)->sum('amount'), 2);

        // Get paginated records
        $records = $baseQuery
            ->orderByDesc('id')
            ->with('user', 'gateway') // removed payout
            ->paginate(config('basic.paginate'));

        $pageTitle = "Search Payout Logs";
        return view('admin.payout.report', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum'));
    }

    public function dailyReport()
    {
        $domains = Api::where('type', 'Admin')->get();
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Withdrawal Report";
        $payoutsByDate = Payout::select(
            DB::raw('DATE(created_at) as payout_date'),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as complete_amount')
        )
            ->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)
            ->groupBy('payout_date')
            ->get();
        // dd($payoutsByDate);

        return view('admin.payout.daily_report', compact('payoutsByDate', 'pageTitle', 'gateways', 'from_date', 'to_date', 'domains'));
    }

    public function dailyReportSearch(Request $request)
    {


        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Withdrawal Report";
        $query = Payout::select(
            DB::raw('DATE(created_at) as payout_date'),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as complete_amount')
        )
            // ->whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])
            ->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->to_date)
            ->when($request->website, function ($query) use ($request) {
                $query->where('api_id', $request->website);
            })
            ->groupBy('payout_date');


        if ($request->filled('gateway')) {
            $query->where('e_wallet_name', $request->gateway);
        }

        $payoutsByDate = $query->get();

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        return view('admin.payout.daily_report', compact('payoutsByDate', 'pageTitle', 'gateways', 'from_date', 'to_date', 'domains'));
    }




    public function search(Request $request)
    {
        $search = $request->all();
        $domains = Api::where('type', 'Admin')->get();
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);





        if (isset($search['export'])) {
            $records = Payout::where('status', '!=', 'Initiate')
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
                ->when($search['domain'], function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->whereHas('payout', function ($subQuery) use ($search) {
                            $subQuery->where('api_id', $search['domain']);
                        })->orWhere('api_id', $search['domain']);
                    });
                })
                ->orderBy('id', 'DESC')
                ->with('user', 'gateway')
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
                if ($item->status == 2) {
                    $status = "Approved";
                } elseif ($item->status == 1) {
                    $status = "Pending";
                } elseif ($item->status == 3) {
                    $status = "Rejected";
                }

                if ($item->status == "Complete") {
                    $status2 = "Transfered";
                } elseif ($item->status == "Pending") {
                    $status2 = "Transfer Pending";
                } elseif ($item->status == "Reject") {
                    $status2 = "Transfer Rejected";
                }

                $data[] = [
                    $item->created_at,
                    $item->trx_id,
                    $item->txn_id,
                    $item->partner_transection_id,
                    $user_name,
                    $user_type,
                    optional($item->method)->name,
                    $item->user_account_no,
                    getAmount($item->amount),
                    $item->charge,
                    getAmount($item->net_amount),
                    $status,
                    $status2,
                    $item->e_wallet_phone_number,
                    $item->source,
                    $item->date_time
                ];

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
            $records = Payout::where('status', '!=', 'Initiate')
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
                        $subQuery->where('trx_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when($search['status'] != 4, function ($query) use ($search) {
                    $query->where('status', $search['status']);
                })
                ->when($search['domain'], function ($query) use ($search) {
                    $query->where('api_id', $search['domain']);
                })

                ->orderBy('id', 'DESC')
                ->with('user', 'gateway')
                ->paginate(config('basic.paginate'));
            $pageTitle = "Search Payout Logs";
            $letest_record = Payout::where('status', '!=', 'Initiate')->orderBy('id', 'DESC')->first()->id;
            return view('admin.payout.logs', compact('records', 'pageTitle', 'domains', 'letest_record'));
        }
    }


    public function index()
    {
        $pageTitle = "Payout Logs";
        $domains = Api::where('type', 'Admin')->get();
        $letest_record = Payout::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->first()->id;
        $records = Payout::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));
        return view('admin.payout.logs', compact('records', 'pageTitle', 'domains', 'letest_record'));
    }

    public function request()
    {
        $pageTitle = "Payout Request";
        $domains = Api::where('type', 'Admin')->get();
        $letest_record = PayoutLog::where('status', '!=', 0)->orderBy('id', 'DESC')->first()->id;
        $records = PayoutLog::whereIn('status', [1, 2])
            ->orderBy('id', 'DESC')
            ->with('user', 'method', 'payout')
            ->where(function ($query) {
                $query->whereHas('payout', function ($subQuery) {
                    $subQuery->whereNotIn('status', ['Complete', 'Reject']);
                })->orWhereDoesntHave('payout');
            })
            ->paginate(config('basic.paginate'));
        return view('admin.payout.logs', compact('records', 'pageTitle', 'domains', 'letest_record'));
    }

    public  function action(Request $request, $id)
    {
        $this->validate($request, [
            'id' => 'required',
            'status' => ['required', Rule::in(['2', '3', '4'])],
        ]);

        DB::beginTransaction();

        try {
            // 1 in pending // 2 success
            $data = PayoutLog::where('id', $request->id)->whereIn('status', [1, 2])->with('user', 'method')->lockForUpdate()->firstOrFail();
            $basic = (object) config('basic');

            if ($request->status == '2') {
                $pre_payout = Payout::where('payout_log_id', $data->id)->lockForUpdate()->first();
                if (!$pre_payout) {
                    $pre_payout = new Payout();
                }
            }
            else
            {
                $payout = Payout::where('payout_log_id', $data->id)->lockForUpdate()->first();
                if(!$payout && $request->status != '3')
                {
                    DB::rollBack();
                    throw new \Exception("This transaction not found.");
                }
            }


            $commit = 0;

            //approved
            if ($request->status == '2') {
                if ($data->method->name == "Nagad" || $data->method->name == "Rocket" || $data->method->name == "Bkash") {
                    //  $result = $this->checkPayoutAmountWithinTime($data);

                    $this->updateLimits();
                    $this->updateEWallets();

                    $current_time = Carbon::now('Asia/Dhaka');





                        $account = EWalletAccount::where('e_wallet_name', $data->method->name)
                            ->where('type', 'Agent')
                            ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                            ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$data->amount])
                            ->where('status', 1)
                            ->where('max_withdrawal_amount', '>=', $data->amount)
                            ->whereIn('account_type', ['Withdrawal', 'Both'])
                            ->where(function ($query) use ($current_time) {
                                $query->where('apply_time_limit', 0)
                                    ->orWhere(function ($query) use ($current_time) {
                                        $query->where('apply_time_limit', 1)
                                            ->where('from_time', '<=', $current_time)
                                            ->where('to_time', '>=', $current_time);
                                    });
                            })
                            ->orderBy('daily_sent', 'asc')
                            ->first();
                        if (!$account) {
                            $account = EWalletAccount::where('e_wallet_name', $data->method->name)
                                ->where('type', 'Merchant')
                                ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                                ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$data->amount])
                                ->where('status', 1)
                                ->where('max_withdrawal_amount', '>=', $data->amount)
                                ->whereIn('account_type', ['Withdrawal', 'Both'])
                                ->where(function ($query) use ($current_time) {
                                    $query->where('apply_time_limit', 0)
                                        ->orWhere(function ($query) use ($current_time) {
                                            $query->where('apply_time_limit', 1)
                                                ->where('from_time', '<=', $current_time)
                                                ->where('to_time', '>=', $current_time);
                                        });
                                })
                                ->orderBy('daily_sent', 'asc')
                                ->first();
                            if (!$account) {
                                $account = EWalletAccount::where('e_wallet_name', $data->method->name)
                                    ->where('type', 'Personal')
                                    ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                                    ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$data->amount])
                                    ->where('status', 1)
                                    ->where('max_withdrawal_amount', '>=', $data->amount)
                                    ->whereIn('account_type', ['Withdrawal', 'Both'])
                                    ->where(function ($query) use ($current_time) {
                                        $query->where('apply_time_limit', 0)
                                            ->orWhere(function ($query) use ($current_time) {
                                                $query->where('apply_time_limit', 1)
                                                    ->where('from_time', '<=', $current_time)
                                                    ->where('to_time', '>=', $current_time);
                                            });
                                    })
                                    ->orderBy('daily_sent', 'asc')
                                    ->first();
                            }
                        }

                    if (!$account) {
                        DB::rollBack();
                        throw new \Exception("No E-wallet account Available at this time to proceed this request.");
                    }

                    if (isset($data->information->PhoneNumber->field_name)) {
                        $user_account_no =  $data->information->PhoneNumber->field_name;
                    } else {
                        $user_account_no =  $data->user_account_no;
                    }

                    $pre_payout->api_id = $data->api_id;
                    $pre_payout->payout_log_id = $data->id;
                    $pre_payout->e_wallet_name = $data->method->name;
                    $pre_payout->amount = $data->amount;
                    $pre_payout->user_account_no = $user_account_no;
                    $pre_payout->e_wallet_phone_number = $account->account_no;
                    $pre_payout->e_wallet_type = $account->type;
                    $pre_payout->status = 'Pending';
                    $pre_payout->save();

                    $data->payout_id = $pre_payout->id;
                    $data->feedback = $request->feedback;
                    $data->save();

                }

                $data->status = 2;
                $data->feedback = $request->feedback;
                $data->save();

                $commit = 1;
                DB::commit();

                $user = $data->user;

                session()->flash('success', 'Approve Successfully');
            } elseif ($request->status == '3') {

                if($data->status == 3)
                {
                    DB::rollBack();
                    throw new \Exception("This transaction already rejected!.");
                }

                $data->status = 3;
                $data->feedback = $request->feedback;
                $data->save();

                if ($payout) {
                    if (!empty($payout->api_id) && $payout->api_id != 0) {
                        if ($payout->status == "Complete") {
                            $partner_api_key = Api::where('id', $payout->api_id)->lockForUpdate()->firstOrFail();
                            $partner_api_key->balance += ($payout->amount + $payout->charge);
                            $partner_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $payout->updated_at;
                            $Log->final_amount = ($payout->amount + $payout->charge);
                            $Log->balance = $partner_api_key->balance;
                            $Log->transection_type = 7;
                            $Log->transection_id = $payout->id;
                            $Log->partner_id = $payout->api_id;
                            $Log->source = 'AdminPanel';
                            $Log->save();

                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $payout->created_at)->get();
                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($payout->amount + $payout->charge);
                                $amount_to_update = round($amount_to_update, 2);
                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                $DailyPartnerSummary_record->save();

                                $summary_log = new DailyPartnerSummaryLog();
                                $summary_log->partner_id = $partner_api_key->id;
                                $summary_log->partner_balance = $partner_api_key->balance;
                                $summary_log->payment_id = $payout->id;
                                $summary_log->total_amount = $payout->amount + $payout->charge;
                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                $summary_log->source = 'AdminPanel';
                                $summary_log->save();
                            }

                            $PartnerCommissions = PartnerCommission::where('transaction_id', $payout->id)->where('type', 2)->where('status', 1)->get();
                            foreach ($PartnerCommissions as $PartnerCommission) {
                                $PartnerCommission->status = 0;
                                $PartnerCommission->save();
                                $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->firstOrFail();
                                $parent_api_key->balance -= $PartnerCommission->profit;
                                $parent_api_key->save();

                                $Log = new Log();
                                $Log->date_time = $PartnerCommission->created_at;
                                $Log->final_amount = -$PartnerCommission->profit;
                                $Log->balance = $parent_api_key->balance;
                                $Log->transection_type = 5;
                                $Log->transection_id = $PartnerCommission->id;
                                $Log->partner_id = $PartnerCommission->from_id;
                                $Log->source = 'AdminPanel';
                                $Log->save();

                                $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                    $amount_to_update = $DailyPartnerSummary_record->closing_balance - ($PartnerCommission->profit);
                                    $amount_to_update = round($amount_to_update, 2);
                                    // $amount_to_update = floor($amount_to_update * 100) / 100;
                                    $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                    $DailyPartnerSummary_record->save();

                                    $summary_log = new DailyPartnerSummaryLog();
                                    $summary_log->partner_id = $parent_api_key->id;
                                    $summary_log->partner_balance = $parent_api_key->balance;
                                    $summary_log->payment_id = $PartnerCommission->id;
                                    $summary_log->total_amount = -$PartnerCommission->profit;
                                    $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                    $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                    $summary_log->source = 'AdminPanel';
                                    $summary_log->save();
                                }
                            }


                            $account = EWalletAccount::where('e_wallet_name', $payout->e_wallet_name)
                                ->where('account_no', $payout->e_wallet_phone_number)
                                ->where('status', 1)
                                ->lockForUpdate()->firstOrFail();
                            if ($account) {
                                //E-Wallet Account Log Save
                                $previous_account_balance = number_format($account->balance, 2, '.', '');

                                $account->balance += $payout->amount;
                                $account->daily_sent -= $payout->amount;
                                $account->monthly_sent -= $payout->amount;
                                $account->send -= $payout->amount;
                                $account->save();

                                $e_wallet_log_save = new EWalletLog();
                                $e_wallet_log_save->previous_balance = $previous_account_balance;
                                $e_wallet_log_save->amount = $payout->amount;
                                $e_wallet_log_save->charge = isset($payout_data->fee) ? $payout_data->fee : 0.00;
                                $e_wallet_log_save->commission = isset($payout_data->commission) ? $payout_data->commission : 0.00;

                                $e_wallet_log_save->final_amount = ($payout->amount + $payout->fee - $payout->commission  );
                                $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                                $e_wallet_log_save->transaction_type = 4;
                                $e_wallet_log_save->transaction_id = $payout->id;
                                $e_wallet_log_save->account_id = $account->id;
                                $e_wallet_log_save->source = "action";
                                $e_wallet_log_save->save();
                            }
                        }
                    }

                    $payout->status = "Reject";
                    $payout->save();
                }

                $user = $data->user;
                $user->balance += $data->net_amount;
                $user->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = getAmount($data->net_amount);
                $transaction->final_balance = $user->balance;
                $transaction->charge = $data->charge;
                $transaction->trx_type = '+';
                $transaction->remarks = getAmount($data->amount) . ' ' . $basic->currency . ' withdraw amount has been refunded';
                if (isset($data->trx_id) && !empty($data->trx_id)) {
                    $transaction->trx_id = $data->trx_id;
                }
                $transaction->save();

                $commit = 1;
                DB::commit();

                $api_endpoint = "";
                $partner_api_key = Api::where('id', $payout->api_id)->where('type', 'Admin')->lockForUpdate()->first();
                if ($partner_api_key) {
                    $api_endpoint = $partner_api_key->api_endpoint_withdrawal;
                    if (!empty($partner_api_key->api_endpoint_withdrawal) && $partner_api_key->website != env('APP_WEBSITE')) {

                        $string_to_hash = json_encode(array(
                            "amount" => strval($this->convertStringToNumber($payout->amount)),
                            "api_key" => $partner_api_key->api_key,
                            "e_wallet_name" => $payout->e_wallet_name,
                            "id" => strval($payout->id),
                            'transaction_type' => 'Withdrawal',
                            "user_account_no" => strval($payout->user_account_no),
                        ));
                        $secretKey = $partner_api_key->secret_key;
                        $hash = hash("sha256", $string_to_hash);
                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                        $timestamp = time();
                        $combined = $hmac . $timestamp;
                        $sign = base64_encode($combined);


                        $array_data = [
                                    'id' => $payout->id,
                                    'partner_transection_id' => $payout->partner_transection_id,
                                    'transaction_type' => 'Withdrawal',
                                    'e_wallet_name' => $payout->e_wallet_name,
                                    'amount' => $this->convertStringToNumber($payout->amount),
                                    'user_account_no' => $payout->user_account_no,
                                    'txn_id' => $payout->txn_id,
                                    'e_wallet_phone_number' => $payout->e_wallet_phone_number,
                                    'e_wallet_type' => $payout->e_wallet_type,
                                    'charges' => $this->convertStringToNumber($payout->charge),
                                    'status' => $payout->status,
                                    'completion_date' => $payout->date,
                                    'completion_time' => $payout->time,
                                    'created_at' => $payout->created_at,
                                    'updated_at' => $payout->updated_at,
                                    'sign' => $sign,
                                    'remarks' => $request->feedback,
                        ];

                        if(!empty($payout->member_id)){
                            $array_data['member_id'] = $payout->member_id;
                        }


                        $requestData = [
                            'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                            'request_url' => $partner_api_key->api_endpoint_withdrawal,
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
                                ->post($partner_api_key->api_endpoint_withdrawal, $array_data);

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
                }
                session()->flash('success', 'Reject Successfully');
            } elseif ($request->status == '4') {
                $this->updateLimits();

                if ($payout->status == "Complete") {
                    DB::rollBack();
                    throw new \Exception("This transaction already completed!.");
                }
                else
                {
                    $payout->status = "Complete";
                    $payout->completions_at = Carbon::now();
                    $payout->save();



                    $data->status = 2;
                    $data->feedback = $request->feedback;
                    $data->save();

                    $net_amount = $payout->amount + $payout->charge;

                    $api_endpoint = "";
                    $partner_api_key = Api::where('id', $payout->api_id)->where('type', 'Admin')->lockForUpdate()->firstOrFail();
                    if ($partner_api_key) {
                        $partner_api_key->balance -= $net_amount;
                        $partner_api_key->save();
                        $api_endpoint = $partner_api_key->api_endpoint_withdrawal;

                        $Log = new Log();
                        $Log->date_time = $payout->updated_at;
                        $Log->final_amount = - ($payout->amount + $payout->charge);
                        $Log->balance = $partner_api_key->balance;
                        $Log->transection_type = 2;
                        $Log->transection_id = $payout->id;
                        $Log->partner_id = $payout->api_id;
                        $Log->source = 'AdminPanel';
                        $Log->save();

                        $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $payout->created_at)->get();
                        foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                            $amount_to_update = $DailyPartnerSummary_record->closing_balance - ($payout->amount + $payout->charge);
                            $amount_to_update = round($amount_to_update, 2);
                            // $amount_to_update = floor($amount_to_update * 100) / 100;
                            $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                            $DailyPartnerSummary_record->save();

                            $summary_log = new DailyPartnerSummaryLog();
                            $summary_log->partner_id = $partner_api_key->id;
                            $summary_log->partner_balance = $partner_api_key->balance;
                            $summary_log->payment_id = $payout->id;
                            $summary_log->total_amount = - ($payout->amount + $payout->charge);
                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                            $summary_log->source = 'AdminPanel';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $payout->id)->where('type', 2)->where('status', 0)->get();
                        foreach ($PartnerCommissions as $PartnerCommission) {
                            $PartnerCommission->status = 1;
                            $PartnerCommission->save();
                            $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->firstOrFail();
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

                        $account = EWalletAccount::where('e_wallet_name', $payout->e_wallet_name)
                            ->where('account_no', $payout->e_wallet_phone_number)
                            ->lockForUpdate()->firstOrFail();
                        if ($account) {
                            //E-Wallet Account Log Save
                            $previous_account_balance = number_format($account->balance, 2, '.', '');

                            $account->balance -= $payout->amount;
                            $account->daily_sent += $payout->amount;
                            $account->monthly_sent += $payout->amount;
                            $account->send += $payout->amount;
                            $account->save();

                            $e_wallet_log_save = new EWalletLog();
                            $e_wallet_log_save->previous_balance = $previous_account_balance;
                            $e_wallet_log_save->amount = -$payout->amount;
                            $e_wallet_log_save->charge = isset($payout->fee) ? $payout->fee : 0.00;
                            $e_wallet_log_save->commission = isset($payout->commission) ? $payout->commission : 0.00;
                            $e_wallet_log_save->final_amount = (-$payout->amount - $payout->fee + $payout->commission  );
                            $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                            $e_wallet_log_save->transaction_type = 2;
                            $e_wallet_log_save->transaction_id = $payout->id;
                            $e_wallet_log_save->account_id = $account->id;
                            $e_wallet_log_save->source = "action";
                            $e_wallet_log_save->save();


                            $e_wallet_charge = 0;
                            $count_payouts = Payout::where('e_wallet_name', $payout->e_wallet_name)->where('e_wallet_phone_number', $payout->e_wallet_phone_number)->where('status', 'Complete')->whereDate('date', $payout->date)->count();
                            if ($count_payouts >= $account->free_transections_day) {
                                $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->where('from_amount', '<=', $payout->amount)->where('to_amount', '>=', $payout->amount)->first();
                                if ($e_wallet_charges) {
                                    $e_wallet_charge = $e_wallet_charges->wcharges;
                                    if ($e_wallet_charges->wcharges_type == 2) {
                                        $e_wallet_charge = $e_wallet_charges->wcharges * $payout->amount / 100;
                                    }
                                } else {
                                    $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->orderBy('to_amount', 'desc')->first();
                                    if ($e_wallet_charges) {
                                        $e_wallet_charge = $e_wallet_charges->wcharges;
                                        if ($e_wallet_charges->wcharges_type == 2) {
                                            $e_wallet_charge = $e_wallet_charges->wcharges * $payout->amount / 100;
                                        }
                                    }
                                }
                            }

                            $payout->e_wallet_charges = $e_wallet_charge;
                            $payout->save();


                        }

                        $commit = 1;
                        DB::commit();

                        if (!empty($api_endpoint) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($payout->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $payout->e_wallet_name,
                                "id" => strval($payout->id),
                                'transaction_type' => 'Withdrawal',
                                "user_account_no" => strval($payout->user_account_no),
                            ));
                            $secretKey = $partner_api_key->secret_key;
                            $hash = hash("sha256", $string_to_hash);
                            $hmac = hash_hmac('sha256', $hash, $secretKey);
                            $timestamp = time();
                            $combined = $hmac . $timestamp;
                            $sign = base64_encode($combined);

                            $array_data = [
                                        'id' => $payout->id,
                                        'partner_transection_id' => $payout->partner_transection_id,
                                        'transaction_type' => 'Withdrawal',
                                        'e_wallet_name' => $payout->e_wallet_name,
                                        'amount' => $this->convertStringToNumber($payout->amount),
                                        'user_account_no' => $payout->user_account_no,
                                        'txn_id' => $payout->txn_id,
                                        'e_wallet_phone_number' => $payout->e_wallet_phone_number,
                                        'e_wallet_type' => $payout->e_wallet_type,
                                        'charges' => $this->convertStringToNumber($payout->charge),
                                        'status' => $payout->status,
                                        'completion_date' => $payout->date,
                                        'completion_time' => $payout->time,
                                        'created_at' => $payout->created_at,
                                        'updated_at' => $payout->updated_at,
                                        'sign' => $sign,
                                        'remarks' => $request->feedback,
                            ];

                            if(!empty($payout->member_id)){
                                $array_data['member_id'] = $payout->member_id;
                            }

                            $requestData = [
                                'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                'request_url' => $partner_api_key->api_endpoint_withdrawal,
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
                                    ->post($api_endpoint, $array_data);

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
                    }
                    session()->flash('success', 'Completed Successfully');
                }
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


    public function getNotification(Request $request)
    {
        $letestRecord = $request->input('letest_record');
        $letest_record = Payout::where('status', '!=', 'Initiate')->orderBy('id', 'DESC')->first()->id;
        if ($letest_record != $letestRecord) {
            $letestRecord = $letest_record;
            return response()->json([
                'message' => 'success',
                'letest_record' => $letestRecord
            ]);
        }

        return response()->json([
            'message' => 'fail',
            'letest_record' => $letestRecord
        ]);
    }


    public function update_e_wallet(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'e_wallet_phone_number' => 'required',
        ]);

        DB::beginTransaction();
        try {

            $pre_payout = Payout::where('id', $request->id)->lockForUpdate()->firstOrFail();

            $pre_payout->e_wallet_phone_number = $request->e_wallet_phone_number;
            if ($pre_payout->status == "Reject" || $pre_payout->status == "Rejected") {
                $pre_payout->status = "Pending";
            }
            $pre_payout->save();

            // $pre_payout = PayoutLog::where('payout_id', $pre_payout->id)->lockForUpdate()->first();
            if ($pre_payout) {
                if ($pre_payout->status == 3) {
                    $pre_payout->status = 1;
                }
            }

            if ($pre_payout->status == "Complete") {
                $pre_e_wallet_phone_number = $pre_payout->e_wallet_phone_number;
                $account = EWalletAccount::where('e_wallet_name', $pre_payout->e_wallet_name)
                    ->where('account_no', $pre_e_wallet_phone_number)
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();

                if ($account) {
                    //E-Wallet Account Log Save
                    $previous_account_balance = number_format($account->balance, 2, '.', '');

                    $account->balance += $pre_payout->amount;
                    $account->daily_sent -= $pre_payout->amount;
                    $account->monthly_sent -= $pre_payout->amount;
                    $account->send -= $pre_payout->amount;
                    $account->fee -= $pre_payout->fee;
                    $account->commission -= $pre_payout->commission;
                    $account->save();

                    $e_wallet_log_save = new EWalletLog();
                    $e_wallet_log_save->previous_balance = $previous_account_balance;
                    $e_wallet_log_save->amount = $pre_payout->amount;
                    $e_wallet_log_save->charge = isset($pre_payout->fee) ? $pre_payout->fee : 0.00;
                    $e_wallet_log_save->commission = isset($pre_payout->commission) ? $pre_payout->commission : 0.00;
                    $e_wallet_log_save->final_amount = ($pre_payout->amount + $pre_payout->fee - $pre_payout->commission  );
                    $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                    $e_wallet_log_save->transaction_type = 2;
                    $e_wallet_log_save->transaction_id = $pre_payout->id;
                    $e_wallet_log_save->account_id = $account->id;
                    $e_wallet_log_save->source = "update_e_wallet";
                    $e_wallet_log_save->save();
                }

                $account2 = EWalletAccount::where('e_wallet_name', $pre_payout->e_wallet_name)
                    ->where('account_no', $request->e_wallet_phone_number)
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();
                if ($account2) {

                    //E-Wallet Account Log Save
                    $previous_account2_balance = number_format($account2->balance, 2, '.', '');

                    $account->balance -= $pre_payout->amount;
                    $account->daily_sent += $pre_payout->amount;
                    $account->monthly_sent += $pre_payout->amount;
                    $account->send += $pre_payout->amount;
                    $account->fee += $pre_payout->fee;
                    $account->commission += $pre_payout->commission;
                    $account2->save();

                    $e_wallet_log_save = new EWalletLog();
                    $e_wallet_log_save->previous_balance = $previous_account2_balance;
                    $e_wallet_log_save->amount = -$pre_payout->amount;
                    $e_wallet_log_save->charge = isset($pre_payout->fee) ? $pre_payout->fee : 0.00;
                    $e_wallet_log_save->commission = isset($pre_payout->commission) ? $pre_payout->commission : 0.00;
                    $e_wallet_log_save->final_amount = (-$pre_payout->amount - $pre_payout->fee + $pre_payout->commission  );
                    $e_wallet_log_save->balance = ($previous_account2_balance + $e_wallet_log_save->final_amount);
                    $e_wallet_log_save->transaction_type = 2;
                    $e_wallet_log_save->transaction_id = $pre_payout->id;
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

        $payout = Payout::where('id', $request->id)->first();
        // $payout_log = PayoutLog::select('feedback')->where('payout_id', $request->id)->first();
        $api_key = Api::where('id', $payout->api_id)->first();

        if (!empty($api_key->api_endpoint_withdrawal) && $api_key->website != env('APP_WEBSITE')) {

            $string_to_hash = json_encode(array(
                "amount" => strval($this->convertStringToNumber($payout->amount)),
                "api_key" => $api_key->api_key,
                "e_wallet_name" => $payout->e_wallet_name,
                "id" => strval($payout->id),
                'transaction_type' => 'Withdrawal',
                "user_account_no" => strval($payout->user_account_no),
            ));
            $secretKey = $api_key->secret_key;
            $hash = hash("sha256", $string_to_hash);
            $hmac = hash_hmac('sha256', $hash, $secretKey);
            $timestamp = time();
            $combined = $hmac . $timestamp;
            $sign = base64_encode($combined);

            $array_data = [
                        'id' => $payout->id,
                        'partner_transection_id' => $payout->partner_transection_id,
                        'transaction_type' => 'Withdrawal',
                        'e_wallet_name' => $payout->e_wallet_name,
                        'amount' => $this->convertStringToNumber($payout->amount),
                        'user_account_no' => $payout->user_account_no,
                        'txn_id' => $payout->txn_id,
                        'e_wallet_phone_number' => $payout->e_wallet_phone_number,
                        'e_wallet_type' => $payout->e_wallet_type,
                        'charges' => $this->convertStringToNumber($payout->charge),
                        'status' => $payout->status,
                        // 'completion_date' => $payout->date,
                        // 'completion_time' => $payout->time,
                        'created_at' => $payout->created_at,
                        'updated_at' => $payout->updated_at,
                        'sign' => $sign,
                        // 'remarks' => $payout_log->feedback,

            ];

            if(!empty($payout->member_id)){
                $array_data['member_id'] = $payout->member_id;
            }


            $requestData = [
                'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                'request_url' => $api_key->api_endpoint_withdrawal,
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
                    ->post($api_key->api_endpoint_withdrawal, $array_data);

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



        return response()->json(['status' => 'error', 'message' => 'Unknown Error', 'code' => '', 'response_payload' => ''], 200);
    }


    public function apis(Request $request)
    {
        $records = Api::where('type', 'Admin')->select(['id', 'name', 'username', 'email', 'phone', 'acc_type', 'website','api_endpoint_deposit', 'api_endpoint_withdrawal', 'redirect_url','api_key', 'secret_key', 'balance', 'min_deposit', 'min_withdrawal', 'status'])->paginate(20);
        $pageTitle = "Manage APIs";
        return view('admin.payout.api', compact('records', 'pageTitle'));
    }

    // Partner controller



    public function apisDelete($id)
    {
        $api = Api::findOrFail($id);
        $api_key = $api->api_key;
        Api::where('website', $api_key)->delete();

        // $api->delete();

        return redirect()->route('admin.apis')->with('success', 'API deleted successfully.');
    }

    public function updateApi(Request $request, $id)
    {
        // Validate input
        $validatedData = $request->validate([
            'website' => 'required|string',
            'name' => 'required|string',
            'username' => 'required|string',
            'status' => 'required',
            'password' => 'nullable|string|min:5',
        ]);

        // Find and update API record
        $api = Api::findOrFail($id);

        // Use mass assignment
        $updateData = $request->only($api->getFillable());

        // Hash password only if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $api->update($updateData);

        return back()->with('success', 'API Updated Successfully');
    }



    public function apisAddByParent(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string|min:5',
        ]);

        // Permissions array
        $permissionsArray = [
            "partner.dashboard", "partner.staff", "partner.storeStaff", "partner.updateStaff",
            "partner.apis.delete", "partner.payment.report", "partner.payment.report.search",
            "partner.payment.report.daily", "partner.payment.report.daily.search",
            "partner.payment.report.all", "partner.payment.report.all.search",
            "partner.payout-log", "partner.payout-request", "partner.payout-log.search",
            "partner.payout-action", "partner.payout-report", "partner.payout-report.search",
            "partner.payout.report.daily", "partner.payout.report.daily.search"
        ];

        // Generate unique secret key
        do {
            $secretKey = bin2hex(random_bytes(32));
        } while (Api::where('secret_key', $secretKey)->exists());

        // Generate unique API key
        do {
            $apiKey = Str::random(32);
        } while (Api::where('api_key', $apiKey)->exists());

        // Create new API entry using mass assignment
        $api = Api::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'website' => $request->website,
            'api_endpoint_deposit' => $request->api_endpoint_deposit,
            'api_endpoint_withdrawal' => $request->api_endpoint_withdrawal,
            'redirect_url' => $request->redirect_url,
            'acc_type' => $request->acc_type,
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
            'admin_access' => $permissionsArray,
            'parent_id' => $request->parent_id,
            'status' => 1,
            'type' => "Admin"
        ]);

        // Clone commissions for the new API
        Commission::where('api_id', $request->parent_id)->get()->each(function ($commission) use ($api) {
            Commission::create([
                'from_amount' => $commission->from_amount,
                'to_amount' => $commission->to_amount,
                'deposit_percentage' => $commission->deposit_percentage,
                'withdrawal_percentage' => $commission->withdrawal_percentage,
                'settlement_percentage' => $commission->settlement_percentage,
                'api_id' => $api->id,
            ]);
        });

        return back()->with('success', 'Added Successfully');
    }


    public function apisAdd(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string',
            'status' => 'required',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Permissions list
        $permissionsArray = [
            "partner.dashboard", "partner.staff", "partner.storeStaff", "partner.updateStaff",
            "partner.apis.delete", "partner.payment.report", "partner.payment.report.search",
            "partner.payment.report.daily", "partner.payment.report.daily.search",
            "partner.payment.report.all", "partner.payment.report.all.search", "partner.payout-log",
            "partner.payout-request", "partner.payout-log.search", "partner.payout-action",
            "partner.payout-report", "partner.payout-report.search", "partner.payout.report.daily",
            "partner.payout.report.daily.search"
        ];

        // Generate unique keys
        $secretKey = $this->generateUniqueKey('secret_key');
        $apiKey = $this->generateUniqueKey('api_key');

        // Create and save API entry
        Api::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password), // Secure password hashing
            'status' => $request->status,
            'sign' => $request->sign,
            'website' => $request->website,
            'api_endpoint_deposit' => $request->api_endpoint_deposit,
            'api_endpoint_withdrawal' => $request->api_endpoint_withdrawal,
            'redirect_url' => $request->redirect_url,
            'acc_type' => $request->acc_type,
            'min_deposit' => $request->min_deposit,
            'min_withdrawal' => $request->min_withdrawal,
            'txn_verification' => $request->txn_verification,
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
            'admin_access' => $permissionsArray,
            'type' => 'Admin',
        ]);

        session()->flash('success', 'Added Successfully');
        return back();
    }

    /**
     * Generates a unique key for the given column.
     */
    private function generateUniqueKey(string $column, int $length = 32): string
    {
        do {
            $key = ($column === 'secret_key') ? bin2hex(random_bytes($length)) : Str::random($length);
        } while (Api::where($column, $key)->exists());

        return $key;
    }

    public function apisBalanceAdd(Request $request)
    {
                DB::beginTransaction();
        try {
            // Determine the amount sign
            $amount = $this->calculateAmount($request->amount, $request->amount_type);
            $charges = $this->calculateCharges($request->amount, $request->charges, $request->charges_type);

            // Fetch API partner and update balance with a lock to prevent race conditions
            $api = Api::where('id', $request->partner_id)->lockForUpdate()->firstOrFail();
            $api->increment('balance', ($amount - $charges));

            // Create a new API transaction record
            $apiTransaction = ApiTransaction::create([
                'amount' => $amount,
                'adjustment' => $request->adjustment,
                'source' => $request->source,
                'txn' => $request->txn,
                'reason' => $request->reason,
                'partner_id' => $request->partner_id,
                'charges' => $charges
            ]);

            // Create transaction log
            Log::create([
                'date_time' => now(),
                'final_amount' => $amount - $charges,
                'balance' => $api->balance,
                'transection_type' => 3,
                'transection_id' => $apiTransaction->id,
                'partner_id' => $request->partner_id,
                'source' => 'APIBalanceAdd'
            ]);

            // Update daily partner summary in bulk
            $this->updateDailyPartnerSummary($api, $apiTransaction, $amount, $charges);
            DB::commit();
            session()->flash('success', 'Successfully Updated Balance');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to Update Balance: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Calculate amount based on type.
     */
    private function calculateAmount($amount, $type)
    {
        return ($type == 2) ? -abs($amount) : abs($amount);
    }

    /**
     * Calculate charges based on type.
     */
    private function calculateCharges($amount, $charges, $type)
    {
        return ($type == 2) ? ($amount * $charges) / 100 : $charges;
    }

    /**
     * Updates the daily partner summary.
     */
    private function updateDailyPartnerSummary($api, $apiTransaction, $amount, $charges)
    {
        $dailySummaries = DailyPartnerSummary::where('api_id', $api->id)
            ->whereDate('created_at', '>=', $apiTransaction->created_at)
            ->get();

        foreach ($dailySummaries as $summary) {
            $summary->increment('closing_balance', round($amount - $charges, 2));

            // Insert summary log
            DailyPartnerSummaryLog::create([
                'partner_id' => $api->id,
                'partner_balance' => $api->balance,
                'payment_id' => $apiTransaction->id,
                'total_amount' => $amount - $charges,
                'summary_id' => $summary->id,
                'closing_balance' => $summary->closing_balance,
                'source' => 'APIBalanceAdd'
            ]);
        }
    }


    public function apisCommission($id)
    {
        $api = Api::findOrFail($id);
        $commissions = Commission::where('api_id', $id)->get();
        $cron_commissions = CronCommission::where('api_id', $id)->get();

        // Fetch end user and parents in a single query
        $end_user = Api::findOrFail($id);
        // Set default values
        $level1_parent_id = $level2_parent_id = 0;
        $level1_parent_name = $level2_parent_name = "";

        // Check and assign parent hierarchy
        if ($end_user->parent) {
            $level1_parent_id = $end_user->parent->id;
            $level1_parent_name = $end_user->parent->name;

            if ($end_user->parent->parent) {
                $level2_parent_id = $end_user->parent->parent->id;
                $level2_parent_name = $end_user->parent->parent->name;
            }
        }

        $pageTitle = "Manage Commissions";
        $api_id = $id;
        $records = "";

        return view('admin.payout.commission', compact(
            'records', 'pageTitle', 'api_id', 'commissions', 'cron_commissions',
            'level1_parent_id', 'level2_parent_id', 'level1_parent_name', 'level2_parent_name'
        ));
    }




    // Acconts

    public function allAccounts(Request $request)
    {
        $this->updateLimits();

        $records = EWalletAccount::with(['apiHits' => function ($query) {
            $query->whereBetween('created_at', [now()->subSeconds(70), now()]);
        }])->paginate(20);

        foreach ($records as $record) {
            $record->live = $record->apiHits ? 1 : 0; // If relation exists, set live = 1
        }

        $pageTitle = "All Accounts";
        return view('admin.payout.accounts', compact('records', 'pageTitle'));
    }


    public function updateLimits()
    {
        $todayDate = now()->toDateString();  // Use Carbon for better date handling
        $thisMonth = now()->month;

        EWalletAccount::where('last_limit_reset', '!=', $todayDate)
            ->update([
                'daily_received' => 0,
                'daily_sent' => 0,
                'last_limit_reset' => $todayDate
            ]);

        EWalletAccount::whereMonth('last_limit_reset', '!=', $thisMonth)
            ->update([
                'monthly_received' => 0,
                'monthly_sent' => 0
            ]);
    }



    public function merchantDelete($id)
    {
        $account =EWalletAccount::where('id', $id)->delete()
        ? redirect()->route('admin.merchant')->with('success', 'Account deleted successfully.')
        : redirect()->route('admin.merchant')->with('error', 'Account not found.');
    }



    public function editAccount($id)
    {
        return view('admin.payout.edit_account', ['account' => EWalletAccount::findOrFail($id), 'pageTitle' => 'Edit Account']);
    }



    public function accountCharges($id)
    {
        $account = EWalletAccount::findOrFail($id);

        return view('admin.payout.account_charges', [
            'records' => '',
            'pageTitle' => 'Manage Commissions',
            'account' => $account,
            'account_id' => $id,
            'commissions' => EWalletCharge::where('account_id', $id)->get(),
            'free_transections_day' => $account->free_transections_day
        ]);
    }




    public function accountBalanceAdd(Request $request)
    {
        DB::beginTransaction();
        try {
            $account = EWalletAccount::where('id', $request->account_id)->lockForUpdate()->firstOrFail();
            $previous_balance = number_format($account->balance, 2, '.', '');
            $amount = $request->amount;
            $isAddition = $request->type == "plus";

            // Update balance based on transaction type
            $account->balance += $isAddition ? $amount : -$amount;
            $account->save();

            // Create EWalletLog entry
            $e_wallet_log = EWalletLog::create([
                'account_id' => $account->id,
                'previous_balance' => $previous_balance,
                'charge' => 0.00,
                'commission' => 0.00,
                'amount' => $isAddition ? $amount : -$amount,
                'final_amount' => $isAddition ? $amount : -$amount,
                'balance' => ($previous_balance + ($isAddition ? $amount : -$amount)),
                'transaction_type' => $isAddition ? 5 : 6,
                'source' => 'accountBalanceAdd',
            ]);

            // Create AccountLog entry
            $transaction = AccountLog::create([
                'amount' => $amount,
                'type' => $request->type,
                'e_wallet_account_id' => $request->account_id,
            ]);

            // Update transaction ID in log
            $e_wallet_log->update(['transaction_id' => $transaction->id]);

            DB::commit();
            session()->flash('success', 'Successfully Updated Balance');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to Update Balance: ' . $e->getMessage());
            return back()->withInput();
        }
    }




    public function accountBalanceEdit(Request $request)
    {
        DB::beginTransaction();
        try {
            $account = EWalletAccount::where('id', $request->account_id)->lockForUpdate()->firstOrFail();
            $difference = $request->amount - $account->balance;
            $differenceLive = $request->live_balance - $account->live_balance;

            if ($difference == 0 && $differenceLive == 0) {
                session()->flash('success', 'Same Balance');
                return back();
            }

            $type = $difference > 0 ? "plus" : "minus";
            $transactionType = $difference > 0 ? 5 : 6;
            $amount = abs($difference); // Ensure positive amount

            $previousBalance = number_format($account->balance, 2, '.', '');

            // Update account balances
            $account->update([
                'balance' => $request->amount,
                'live_balance' => $request->live_balance
            ]);

            // Create new transaction log
            $transaction = AccountLog::create([
                'amount' => $amount,
                'type' => $type,
                'e_wallet_account_id' => $request->account_id
            ]);

            // Create wallet log
            EWalletLog::create([
                'account_id' => $account->id,
                'previous_balance' => $previousBalance,
                'amount' => $amount,
                'charge' => 0.00,
                'commission' => 0.00,
                'final_amount' => $amount,
                'balance' => ($previousBalance + ($type === "plus" ? $amount : -$amount)),
                'transaction_type' => $transactionType,
                'transaction_id' => $transaction->id,
                'source' => "accountBalanceEdit"
            ]);

            DB::commit();
            session()->flash('success', 'Successfully Updated Balance');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to Update Balance: ' . $e->getMessage());
            return back()->withInput();
        }
    }




    public function updateStatus($id)
    {
        $record = EWalletAccount::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        $record->live = ApiHit::where('e_wallet_name', $record->e_wallet_name)
            ->where('acc_no', $record->account_no)
            ->whereBetween('created_at', [now()->subSeconds(70), now()])
            ->exists() ? 1 : 0;

        return response()->json([
            'success' => true,
            'live'    => $record->live,
            'id'      => $id
        ]);
    }


    public function apisCommissionAdd(Request $request)
    {

        $cron_commissions = CronCommission::where('api_id', $request->api_id)->get();
        foreach ($cron_commissions as $cron_commission) {
            $cron_commission->delete();

        }

        $new = 0;
        $commissions = Commission::where('api_id', $request->api_id)->get();
        foreach ($commissions as $commission) {
            $new = 1;
            // if(!in_array($commission->id, $request->id)){
            //     $commission->delete();
            // }

        }

        $count = count($request->from_amount);

        for ($i = 0; $i < $count; $i++) {

            $new_commission = Commission::where('id', $request->id[$i])->first();
            if($new_commission){
                $commission_id = $new_commission->id;
            }else{
                $commission_id = 0;
            }

            if($new==0){
                if(!$new_commission){
                    $new_commission = new Commission;
                }
                $new_commission->from_amount = $request->from_amount[$i];
                $new_commission->to_amount = $request->to_amount[$i];
                $new_commission->deposit_percentage = $request->deposit_percentage[$i];
                $new_commission->withdrawal_percentage = $request->withdrawal_percentage[$i];
                $new_commission->settlement_percentage = $request->settlement_percentage[$i];
                $new_commission->api_id = $request->api_id;
                if (isset($request->level1_parent_id[$i])) {
                    $new_commission->parent_id = $request->level1_parent_id[$i];
                    $new_commission->parent_deposit_percentage = $request->parent_deposit_percentage[$i];
                    $new_commission->parent_withdrawal_percentage = $request->parent_withdrawal_percentage[$i];
                }

                if (isset($request->level2_parent_id[$i])) {
                    $new_commission->parent2_id = $request->level2_parent_id[$i];
                    $new_commission->parent2_deposit_percentage = $request->parent2_deposit_percentage[$i];
                    $new_commission->parent2_withdrawal_percentage = $request->parent2_withdrawal_percentage[$i];
                }
                $new_commission->save();
            }else{
                $cron_commission = new CronCommission;
                $cron_commission->from_amount = $request->from_amount[$i];
                $cron_commission->to_amount = $request->to_amount[$i];
                $cron_commission->deposit_percentage = $request->deposit_percentage[$i];
                $cron_commission->withdrawal_percentage = $request->withdrawal_percentage[$i];
                $cron_commission->settlement_percentage = $request->settlement_percentage[$i];
                $cron_commission->api_id = $request->api_id;
                $cron_commission->commission_id = $commission_id;
                if (isset($request->level1_parent_id[$i])) {
                    $cron_commission->parent_id = $request->level1_parent_id[$i];
                    $cron_commission->parent_deposit_percentage = $request->parent_deposit_percentage[$i];
                    $cron_commission->parent_withdrawal_percentage = $request->parent_withdrawal_percentage[$i];
                }

                if (isset($request->level2_parent_id[$i])) {
                    $cron_commission->parent2_id = $request->level2_parent_id[$i];
                    $cron_commission->parent2_deposit_percentage = $request->parent2_deposit_percentage[$i];
                    $cron_commission->parent2_withdrawal_percentage = $request->parent2_withdrawal_percentage[$i];
                }
                $cron_commission->save();
            }
        }
        session()->flash('success', 'Successfully Updated');
        return back();
    }

    public function apiCommissionsDetail($id)
    {

        $records = PartnerCommission::with('api')
        ->select('api_id', 'from_id', \DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) AS sum_amount_type_1'))
        ->selectRaw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) AS sum_charges_type_1')
        ->selectRaw('SUM(CASE WHEN type = 1 THEN total_amount ELSE 0 END) AS sum_total_amount_type_1')
        ->selectRaw('SUM(CASE WHEN type = 1 THEN profit ELSE 0 END) AS sum_profit_type_1')
        ->selectRaw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) AS sum_amount_type_2')
        ->selectRaw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) AS sum_charges_type_2')
        ->selectRaw('SUM(CASE WHEN type = 2 THEN total_amount ELSE 0 END) AS sum_total_amount_type_2')
        ->selectRaw('SUM(CASE WHEN type = 2 THEN profit ELSE 0 END) AS sum_profit_type_2')
        ->where('from_id', $id)
        ->where('status', 1)
        ->groupBy('api_id', 'from_id') // Add 'from_id' here
        ->orderByDesc('id')
        ->get();

        $pageTitle = "Partners Commission Summary";
        $partners = Api::where('type', 'Admin')->get();
        return view('admin.payout.commission_summary', compact('records', 'pageTitle', 'partners'));
    }

    public function apiCommissionsCalculate($id)
    {
        if (!Session::has('previousapiid')) {
            Session::put('previousapiid', $id);
            $previousapiid = $id;
        } else {
            $previousapiid = Session::get('previousapiid');
        }

        if ($previousapiid != $id) {
            Session::put('fundid', 0);
            $fundid = 0;
            Session::put('payoutid', 0);
            $payoutid = 0;
            Session::put('apiid', 0);
            $apiid = 0;
            Session::put('fistpart', 0);
            $fistpart = 0;
            Session::put('fundidc', 0);
            $fundidc = 0;
            Session::put('payoutidc', 0);
            $payoutidc = 0;
        }

        if (!Session::has('fistpart')) {
            Session::put('fistpart', 0);
            $fistpart = 0;
        } else {
            $fistpart = Session::get('fistpart');
        }


        if (!Session::has('fundid')) {
            Session::put('fundid', 0);
            $fundid = 0;
        } else {
            $fundid = Session::get('fundid');
        }

        if (!Session::has('payoutid')) {
            Session::put('payoutid', 0);
            $payoutid = 0;
        } else {
            $payoutid = Session::get('payoutid');
        }

        if (!Session::has('fundidc')) {
            Session::put('fundidc', 0);
            $fundidc = 0;
        } else {
            $fundidc = Session::get('fundidc');
        }

        if (!Session::has('payoutidc')) {
            Session::put('payoutidc', 0);
            $payoutidc = 0;
        } else {
            $payoutidc = Session::get('payoutidc');
        }

        if (!Session::has('apiid')) {
            Session::put('apiid', 0);
            $apiid = 0;
        } else {
            $apiid = Session::get('apiid');
        }


        if (!Session::has('apiidcc')) {
            Session::put('apiidcc', 0);
            $apiidcc = 0;
        } else {
            $apiidcc = Session::get('apiidcc');
        }


        $apis = Api::select('id', 'parent_id')->where('type', 'Admin')->where('id', '>=', $apiid)->where('parent_id', $id)->get();
        $preapis  = [];
        $ccapis = Api::select('id', 'parent_id')->where('type', 'Admin')->where('parent_id', $id)->get();
        foreach ($ccapis as $api) {
            $apis_cc = Api::select('id', 'parent_id')->where('type', 'Admin')->where('id', '>=', $apiidcc)->where('parent_id', $api->id)->get();
            foreach ($apis_cc as $apis_c) {
                $preapis[]  = $apis_c;
            }
        }

        $count = 0;
        if ($fistpart == 0) {
            foreach ($apis as $api) {
                Session::put('apiid', $api->id);
                $count++;
                if ($count > 2) {
                    Session::put('fundid', 0);
                    Session::put('payoutid', 0);
                    $fundid = 0;
                    $payoutid = 0;
                }

                $sum = Payment::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->where('api_id', $api->id)
                    ->where('status', 'Complete')
                    ->sum('amount');

                if (!$sum) {
                    $sum = 0;
                }

                $charge = 0;
                $commissions = Commission::select('id', 'deposit_percentage', 'parent_id', 'parent_deposit_percentage', 'parent2_id', 'parent2_deposit_percentage')->where('api_id', $api->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
                if ($commissions) {
                    // $charge = $commissions->deposit_percentage * $order->amount / 100;
                } else {
                    $commissions = Commission::select('id', 'deposit_percentage', 'parent_id', 'parent_deposit_percentage', 'parent2_id', 'parent2_deposit_percentage')->where('api_id', $api->id)->orderBy('to_amount', 'desc')->first();
                }

                if (isset($commissions) && (($commissions->parent_id > 0 && $commissions->parent_deposit_percentage > 0))) {
                    $orders = Fund::select('id', 'amount')->where('api_id', $api->id)->where('id', '>=', $fundid)->where('charge', '>', 0)->where('status', 1)->get();
                    foreach ($orders as $order) {
                        Session::put('fundid', $order->id);


                        $charge = $commissions->deposit_percentage * $order->amount / 100;

                        if ($commissions) {
                            if ($commissions->parent_id > 0) {
                                $PartnerCommission = PartnerCommission::select('id')->where('api_id', $api->id)->where('from_id', $commissions->parent_id)->where('type', 1)->where('status', 1)->where('transaction_id', $order->id)->first();
                                if (is_null($PartnerCommission)) {
                                    if ($commissions->parent_deposit_percentage > 0) {
                                        $PartnerCommission = new PartnerCommission();
                                        $PartnerCommission->api_id = $api->id;
                                        $PartnerCommission->from_id = $api->parent_id;
                                        $PartnerCommission->type = 1;
                                        $PartnerCommission->amount = $order->amount;
                                        $PartnerCommission->charges = $charge;
                                        $PartnerCommission->total_amount = $order->amount - $charge;
                                        $PartnerCommission->charges_p = $commissions->deposit_percentage;
                                        $profit_p = $commissions->parent_deposit_percentage;
                                        $profit = $profit_p * $order->amount / 100;
                                        $PartnerCommission->profit = $profit;
                                        $PartnerCommission->profit_p = $profit_p;
                                        $PartnerCommission->transaction_id = $order->id;
                                        $PartnerCommission->status = 1;
                                        $PartnerCommission->save();

                                        $parent_api_key = Api::select('id', 'balance')->where('id', $api->parent_id)->first();
                                        $parent_api_key->balance += $profit;
                                        $parent_api_key->save();

                                        $Log = new Log();
                                        $Log->date_time = $PartnerCommission->created_at;
                                        $Log->final_amount = $PartnerCommission->profit;
                                        $Log->balance = $parent_api_key->balance;
                                        $Log->transection_type = 5;
                                        $Log->transection_id = $PartnerCommission->id;
                                        $Log->partner_id = $PartnerCommission->from_id;
                                        $Log->source = 'apiCommissionsCalculate';
                                        $Log->save();

                                        $DailyPartnerSummary_records =  DailyPartnerSummary::select('id', 'closing_balance')->where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                        foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                            $amount_to_update = $DailyPartnerSummary_record->closing_balance + $profit;
                                            $amount_to_update = round($amount_to_update, 2);
                                            // $amount_to_update = floor($amount_to_update * 100) / 100;
                                            $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                            $DailyPartnerSummary_record->save();

                                            $summary_log = new DailyPartnerSummaryLog();
                                            $summary_log->partner_id = $parent_api_key->id;
                                            $summary_log->partner_balance = $parent_api_key->balance;
                                            $summary_log->payment_id = $PartnerCommission->id;
                                            $summary_log->total_amount = $profit;
                                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                            $summary_log->source = 'apiCommissionsCalculate';
                                            $summary_log->save();
                                        }

                                        // $main_parent_commissions = Commission::where('id', $parent_commissions->parent_id)->first();

                                    }
                                }
                            }
                        }
                    }
                }


                $sum = Payout::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->where('api_id', $api->id)
                    ->where('status', 'Complete')
                    ->sum('amount');

                if (!$sum) {
                    $sum = 0;
                }

                $charge = 0;
                $commissions = Commission::select('id', 'withdrawal_percentage', 'parent_id', 'parent_withdrawal_percentage', 'parent2_id', 'parent2_withdrawal_percentage')->where('api_id', $api->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
                if ($commissions) {
                    // $charge = $commissions->withdrawal_percentage * $payout->amount / 100;
                } else {
                    $commissions = Commission::select('id', 'withdrawal_percentage', 'parent_id', 'parent_withdrawal_percentage', 'parent2_id', 'parent2_withdrawal_percentage')->where('api_id', $api->id)->orderBy('to_amount', 'desc')->first();
                }

                if (isset($commissions) && (($commissions->parent_id > 0 && $commissions->parent_withdrawal_percentage > 0))) {
                    $payouts = Payout::select('id', 'amount')->where('api_id', $api->id)->where('id', '>=', $payoutid)->where('charge', '>', 0)->where('status', 'Complete')->get();
                    foreach ($payouts as $payout) {
                        Session::put('payoutid', $payout->id);

                        $charge = $commissions->withdrawal_percentage * $payout->amount / 100;


                        if ($commissions) {
                            if ($commissions->parent_id > 0) {
                                $PartnerCommission = PartnerCommission::select('id')->where('api_id', $api->id)->where('from_id', $commissions->parent_id)->where('type', 2)->where('status', 1)->where('transaction_id', $payout->id)->first();
                                if (!$PartnerCommission) {

                                    if ($commissions->parent_id > 0 && $commissions->parent_withdrawal_percentage > 0) {
                                        $PartnerCommission = new PartnerCommission();
                                        $PartnerCommission->api_id = $api->id;
                                        $PartnerCommission->from_id = $api->parent_id;
                                        $PartnerCommission->type = 2;
                                        $PartnerCommission->amount = $payout->amount;
                                        $PartnerCommission->charges = $charge;
                                        $PartnerCommission->total_amount = $payout->amount + $charge;
                                        $PartnerCommission->charges_p = $commissions->withdrawal_percentage;
                                        $profit_p = $commissions->parent_withdrawal_percentage;
                                        $profit = $profit_p * $payout->amount / 100;
                                        $PartnerCommission->profit = $profit;
                                        $PartnerCommission->profit_p = $profit_p;
                                        $PartnerCommission->transaction_id = $payout->id;
                                        $PartnerCommission->status = 1;
                                        $PartnerCommission->save();

                                        $parent_api_key = Api::select('id', 'balance')->where('id', $api->parent_id)->first();
                                        $parent_api_key->balance += $profit;
                                        $parent_api_key->save();

                                        $Log = new Log();
                                        $Log->date_time = $PartnerCommission->created_at;
                                        $Log->final_amount = $PartnerCommission->profit;
                                        $Log->balance = $parent_api_key->balance;
                                        $Log->transection_type = 5;
                                        $Log->transection_id = $PartnerCommission->id;
                                        $Log->partner_id = $PartnerCommission->from_id;
                                        $Log->source = 'apiCommissionsCalculate';
                                        $Log->save();

                                        $DailyPartnerSummary_records =  DailyPartnerSummary::select('id', 'closing_balance')->where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                        foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                            $amount_to_update = $DailyPartnerSummary_record->closing_balance + $profit;
                                            $amount_to_update = round($amount_to_update, 2);
                                            // $amount_to_update = floor($amount_to_update * 100) / 100;
                                            $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                            $DailyPartnerSummary_record->save();

                                            $summary_log = new DailyPartnerSummaryLog();
                                            $summary_log->partner_id = $parent_api_key->id;
                                            $summary_log->partner_balance = $parent_api_key->balance;
                                            $summary_log->payment_id = $PartnerCommission->id;
                                            $summary_log->total_amount = $profit;
                                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                            $summary_log->source = 'apiCommissionsCalculate';
                                            $summary_log->save();
                                        }

                                        // $main_parent_commissions = Commission::where('id', $parent_commissions->parent_id)->first();

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $count = 0;
        foreach ($preapis as $api) {
            Session::put('fistpart', 1);
            Session::put('apiidcc', $api->id);
            $count++;
            if ($count > 2) {
                Session::put('fundidc', 0);
                Session::put('payoutidc', 0);
                $fundidc = 0;
                $payoutidc = 0;
            }

            $sum = Payment::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('api_id', $api->id)
                ->where('status', 'Complete')
                ->sum('amount');

            if (!$sum) {
                $sum = 0;
            }

            $charge = 0;
            $commissions = Commission::select('id', 'deposit_percentage', 'parent_id', 'parent_deposit_percentage', 'parent2_id', 'parent2_deposit_percentage')->where('api_id', $api->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
            if ($commissions) {
                // $charge = $commissions->deposit_percentage * $order->amount / 100;
            } else {
                $commissions = Commission::select('id', 'deposit_percentage', 'parent_id', 'parent_deposit_percentage', 'parent2_id', 'parent2_deposit_percentage')->where('api_id', $api->id)->orderBy('to_amount', 'desc')->first();
            }

            if (isset($commissions) && (($commissions->parent2_id > 0 && $commissions->parent2_deposit_percentage > 0))) {
                $orders = Fund::select('id', 'amount')->where('api_id', $api->id)->where('id', '>=', $fundidc)->where('charge', '>', 0)->where('status', 1)->get();
                foreach ($orders as $order) {
                    Session::put('fundidc', $order->id);


                    $charge = $commissions->deposit_percentage * $order->amount / 100;

                    if ($commissions) {
                        if ($commissions->parent2_id > 0) {
                            $PartnerCommission = PartnerCommission::select('id')->where('api_id', $api->id)->where('from_id', $commissions->parent2_id)->where('type', 1)->where('status', 1)->where('transaction_id', $order->id)->first();
                            if (is_null($PartnerCommission)) {

                                if ($commissions->parent2_deposit_percentage > 0) {
                                    $PartnerCommission = new PartnerCommission();
                                    $PartnerCommission->api_id = $api->id;
                                    $PartnerCommission->from_id = $commissions->parent2_id;
                                    $PartnerCommission->type = 1;
                                    $PartnerCommission->amount = $order->amount;
                                    $PartnerCommission->charges = $charge;
                                    $PartnerCommission->total_amount = $order->amount - $charge;
                                    $PartnerCommission->charges_p = $commissions->deposit_percentage;
                                    $profit_p = $commissions->parent2_deposit_percentage;
                                    $profit = $profit_p * $order->amount / 100;
                                    $PartnerCommission->profit = $profit;
                                    $PartnerCommission->profit_p = $profit_p;
                                    $PartnerCommission->transaction_id = $order->id;
                                    $PartnerCommission->status = 1;
                                    $PartnerCommission->save();

                                    $main_parent_api_key = Api::select('id', 'balance')->where('id', $commissions->parent2_id)->first();
                                    $main_parent_api_key->balance += $profit;
                                    $main_parent_api_key->save();

                                    $Log = new Log();
                                    $Log->date_time = $PartnerCommission->created_at;
                                    $Log->final_amount = $PartnerCommission->profit;
                                    $Log->balance = $main_parent_api_key->balance;
                                    $Log->transection_type = 5;
                                    $Log->transection_id = $PartnerCommission->id;
                                    $Log->partner_id = $PartnerCommission->from_id;
                                    $Log->source = 'apiCommissionsCalculate';
                                    $Log->save();

                                    $DailyPartnerSummary_records =  DailyPartnerSummary::select('id', 'closing_balance')->where('api_id', $main_parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + $profit;
                                        $amount_to_update = round($amount_to_update, 2);
                                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                        $DailyPartnerSummary_record->save();

                                        $summary_log = new DailyPartnerSummaryLog();
                                        $summary_log->partner_id = $main_parent_api_key->id;
                                        $summary_log->partner_balance = $main_parent_api_key->balance;
                                        $summary_log->payment_id = $PartnerCommission->id;
                                        $summary_log->total_amount = $profit;
                                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                        $summary_log->source = 'apiCommissionsCalculate';
                                        $summary_log->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }


            $sum = Payout::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('api_id', $api->id)
                ->where('status', 'Complete')
                ->sum('amount');

            if (!$sum) {
                $sum = 0;
            }

            $charge = 0;
            $commissions = Commission::select('id', 'withdrawal_percentage', 'parent_id', 'parent_withdrawal_percentage', 'parent2_id', 'parent2_withdrawal_percentage')->where('api_id', $api->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
            if ($commissions) {
                // $charge = $commissions->withdrawal_percentage * $payout->amount / 100;
            } else {
                $commissions = Commission::select('id', 'withdrawal_percentage', 'parent_id', 'parent_withdrawal_percentage', 'parent2_id', 'parent2_withdrawal_percentage')->where('api_id', $api->id)->orderBy('to_amount', 'desc')->first();
            }

            if (isset($commissions) && (($commissions->parent2_id > 0 && $commissions->parent2_withdrawal_percentage > 0))) {
                $payouts = Payout::select('id', 'amount')->where('api_id', $api->id)->where('id', '>=', $payoutidc)->where('charge', '>', 0)->where('status', 'Complete')->get();
                foreach ($payouts as $payout) {
                    Session::put('payoutidc', $payout->id);

                    $charge = $commissions->withdrawal_percentage * $payout->amount / 100;


                    if ($commissions) {

                        if ($commissions->parent2_id > 0) {
                            $PartnerCommission = PartnerCommission::select('id')->where('api_id', $api->id)->where('from_id', $commissions->parent2_id)->where('type', 2)->where('status', 1)->where('transaction_id', $payout->id)->first();
                            if (!$PartnerCommission) {

                                if ($commissions->parent2_id > 0 && $commissions->parent2_withdrawal_percentage > 0) {
                                    $PartnerCommission = new PartnerCommission();
                                    $PartnerCommission->api_id = $api->id;
                                    $PartnerCommission->from_id = $commissions->parent2_id;
                                    $PartnerCommission->type = 2;
                                    $PartnerCommission->amount = $payout->amount;
                                    $PartnerCommission->charges = $charge;
                                    $PartnerCommission->total_amount = $payout->amount + $charge;
                                    $PartnerCommission->charges_p = $commissions->withdrawal_percentage;
                                    $profit_p = $commissions->parent2_withdrawal_percentage;
                                    $profit = $profit_p * $payout->amount / 100;
                                    $PartnerCommission->profit = $profit;
                                    $PartnerCommission->profit_p = $profit_p;
                                    $PartnerCommission->transaction_id = $payout->id;
                                    $PartnerCommission->status = 1;
                                    $PartnerCommission->save();

                                    $main_parent_api_key = Api::select('id', 'balance')->where('id', $commissions->parent2_id)->first();
                                    $main_parent_api_key->balance += $profit;
                                    $main_parent_api_key->save();

                                    $Log = new Log();
                                    $Log->date_time = $PartnerCommission->created_at;
                                    $Log->final_amount = $PartnerCommission->profit;
                                    $Log->balance = $main_parent_api_key->balance;
                                    $Log->transection_type = 5;
                                    $Log->transection_id = $PartnerCommission->id;
                                    $Log->partner_id = $PartnerCommission->from_id;
                                    $Log->source = 'apiCommissionsCalculate';
                                    $Log->save();


                                    $DailyPartnerSummary_records =  DailyPartnerSummary::select('id', 'closing_balance')->where('api_id', $main_parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + $profit;
                                        $amount_to_update = round($amount_to_update, 2);
                                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                        $DailyPartnerSummary_record->save();

                                        $summary_log = new DailyPartnerSummaryLog();
                                        $summary_log->partner_id = $main_parent_api_key->id;
                                        $summary_log->partner_balance = $main_parent_api_key->balance;
                                        $summary_log->payment_id = $PartnerCommission->id;
                                        $summary_log->total_amount = $profit;
                                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                        $summary_log->source = 'apiCommissionsCalculate';
                                        $summary_log->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.api.commissions.detail', ['id' => $id])->with('success', 'Operation Successful');
    }


    //Add Accounts

    public function addAccount()
    {
        $pageTitle = "Create Account";
        return view('admin.payout.create_account', compact('pageTitle'));
    }



    public function createAccount(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'e_wallet_name' => 'required|string',
            'account_no' => 'required|string',
            'type' => 'required|string',
            'daily_limit' => 'nullable|numeric',
            'monthly_limit' => 'nullable|numeric',
            'status' => 'required|numeric',
        ]);

        $newAccount = new EWalletAccount;

        $newAccount->e_wallet_name = $request->e_wallet_name;
        $newAccount->account_no = $request->account_no;
        $newAccount->type = $request->type;
        $newAccount->daily_limit = $request->daily_limit;
        $newAccount->monthly_limit = $request->monthly_limit;
        $newAccount->status = $request->status;
        $newAccount->account_type = $request->account_type;

        $newAccount->daily_limit_withdrawal = $request->daily_limit_withdrawal;
        $newAccount->monthly_limit_withdrawal = $request->monthly_limit_withdrawal;
        $newAccount->apply_time_limit = $request->apply_time_limit;

        if ($request->apply_time_limit == 1) {
            $newAccount->from_time = $request->from_time;
            $newAccount->to_time = $request->to_time;
        }

        $newAccount->deposit_daily_limit_percentage = $request->deposit_daily_limit_percentage;
        $newAccount->withdrawal_daily_limit_percentage = $request->withdrawal_daily_limit_percentage;
        $newAccount->deposit_monthly_limit_percentage = $request->deposit_monthly_limit_percentage;
        $newAccount->withdrawal_monthly_limit_percentage = $request->withdrawal_monthly_limit_percentage;


        if ($request->filled('max_withdrawal_amount')) {
            $newAccount->max_withdrawal_amount = $request->max_withdrawal_amount;
        }

        if ($request->hasFile('image')) {

            try {
                $newAccount->image = $this->uploadImage($request->image, config('location.withdraw.path'), config('location.withdraw.size'));
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }

        $newAccount->save();


        session()->flash('success', 'Saved Successfully');
        return back();
    }

    public function merchant(Request $request)
    {
        $records = EWalletAccount::get();
        $pageTitle = "Merchant Accounts";
        return view('admin.payout.merchant', compact('records', 'pageTitle'));
    }

    public function merchantAdd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'e_wallet_name' => 'required|string',
            'account_no' => 'required|string',
        ]);

        $newAccount = new EWalletAccount;

        $newAccount->e_wallet_name = $request->e_wallet_name;
        $newAccount->account_no = $request->account_no;
        $newAccount->type = 'Merchant';

        $newAccount->save();
        session()->flash('success', 'Added Successfully');
        return back();
    }

    public function updateAccount(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'e_wallet_name' => 'required|string',
            'account_no' => 'required|string',
            'type' => 'required|string',
            'daily_limit' => 'nullable|numeric',
            'monthly_limit' => 'nullable|numeric',
            'status' => 'required|numeric',
        ]);

        $newAccount = EWalletAccount::findOrFail($id);
        $newAccount->e_wallet_name = $request->e_wallet_name;
        $newAccount->account_no = $request->account_no;
        $newAccount->type = $request->type;
        $newAccount->daily_limit = $request->daily_limit;
        $newAccount->monthly_limit = $request->monthly_limit;
        $newAccount->status = $request->status;
        $newAccount->account_type = $request->account_type;


        $newAccount->daily_limit_withdrawal = $request->daily_limit_withdrawal;
        $newAccount->monthly_limit_withdrawal = $request->monthly_limit_withdrawal;
        $newAccount->apply_time_limit = $request->apply_time_limit;

        if ($request->apply_time_limit == 1) {
            $newAccount->from_time = $request->from_time;
            $newAccount->to_time = $request->to_time;
        }

        $newAccount->deposit_daily_limit_percentage = $request->deposit_daily_limit_percentage;
        $newAccount->withdrawal_daily_limit_percentage = $request->withdrawal_daily_limit_percentage;
        $newAccount->deposit_monthly_limit_percentage = $request->deposit_monthly_limit_percentage;
        $newAccount->withdrawal_monthly_limit_percentage = $request->withdrawal_monthly_limit_percentage;

        if ($request->filled('max_withdrawal_amount')) {
            $newAccount->max_withdrawal_amount = $request->max_withdrawal_amount;
        }

        if ($request->hasFile('image')) {

            try {
                $newAccount->image = $this->uploadImage($request->image, config('location.withdraw.path'), config('location.withdraw.size'));
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }

        $newAccount->save();

        return redirect()->route('admin.accounts')->with('success', 'Saved Successfully.');
    }

    public function accountChargesAdd(Request $request)
    {
        // free_transections_day
        $account = EWalletAccount::findOrFail($request->account_id);
        $account->free_transections_day = $request->free_transections_day;
        $account->save();

        $commissions = EWalletCharge::where('account_id', $request->account_id)->get();
        foreach ($commissions as $commission) {
            $commission->delete();
        }
        $count = count($request->from_amount);
        for ($i = 0; $i < $count; $i++) {

            $new_commission = new EWalletCharge;
            $new_commission->from_amount = $request->from_amount[$i];
            $new_commission->to_amount = $request->to_amount[$i];
            $new_commission->charges = $request->charges[$i];
            $new_commission->charges_type = $request->charges_type[$i];

            $new_commission->wcharges = $request->wcharges[$i];
            $new_commission->wcharges_type = $request->wcharges_type[$i];

            $new_commission->account_id = $request->account_id;
            $new_commission->save();
        }
        session()->flash('success', 'Successfully Updated');
        return back();
    }

    public function apisBalanceAddGet()
    {
        $domains = Api::where('type', 'Admin')
            ->where(fn($query) => $query->where('website', '!=', env('APP_WEBSITE'))
                ->orWhereNull('website'))
            ->get();

        return view('admin.payout.add_balance', [
            'domains' => $domains,
            'pageTitle' => 'Add Partner Balance / Adjustment'
        ]);
    }





    public function settlements()
{
    $records = Settlement::with('api')->latest('id')->paginate(10);

    $gateways = Settlement::select('source_name', DB::raw('COUNT(*) as count'))
        ->groupBy('source_name')
        ->get();

    $pageTitle = "Partners Settlements History";
    $partners = Api::where('type', 'Admin')->get();

    return view('admin.payout.settlement', compact('records', 'pageTitle', 'gateways', 'partners'));
}



    public function storeSettlement(Request $request)
    {
        $sum = Settlement::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('partner_id', $request->partner)
            ->where('status', '1')
            ->sum('amount');

        $api_key = Api::where('id', $request->partner)->first();
        $charge = 0;
        $commissions = Commission::where('api_id', $api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->settlement_percentage * $request->amount / 100;
        } else {
            $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->settlement_percentage * $request->amount / 100;
            }
        }

        if ($api_key->balance < $request->amount + $charge) {
            session()->flash('error', 'you can only enter amount less than to your transferable settlement balance.');
            return back();
        }

        $settlement = new Settlement();
        $settlement->source = $request->source;
        $settlement->source_name = $request->source_name;
        $settlement->account_no = $request->account_no;
        $settlement->amount = $request->amount;
        $settlement->charges = $charge;
        $settlement->net_amount = $request->amount + $charge;
        $settlement->partner_id = $api_key->id;
        $settlement->status = 0;
        $settlement->save();

        session()->flash('success', 'Saved Successfully');
        return back();
    }



    public function settlementSearch(Request $request)
    {

        $partners = Api::where('type', 'Admin')->get();

        $records = Settlement::with('api');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $records->whereDate('created_at', '>=', $request->from_date);
            $records->whereDate('created_at', '<=', $request->to_date);
        } elseif (!empty($request->from_date)) {
            $records->whereDate('created_at', '>=', $request->from_date);
        } elseif (!empty($request->to_date)) {
            $records->whereDate('created_at', '<=', $request->to_date);
        }

        if (!empty($request->gateway)) {
            $records->where('source_name', $request->gateway);
        }

        if (!empty($request->partner)) {
            $records->where('partner_id', $request->partner);
        }

        if (!empty($request->status) || $request->status == '0') {
            $records->where('status', $request->status);
        }

        $records = $records->orderBy('id', 'DESC')->get();


        // $gateways = Settlement::groupBy('source_name')->get();
        $gateways = Settlement::select('source_name', DB::raw('COUNT(*) as count'))
        ->groupBy('source_name')
        ->get();
        $pageTitle = "Search Settlements History";
        return view('admin.payout.settlement', compact('records', 'pageTitle', 'gateways', 'partners'));
    }


    public function approveSettlement($id)
    {
        DB::beginTransaction();
        try {
            // $Settlement = Settlement::findOrFail($id);
            $Settlement = Settlement::where('id', $id)
            ->where('status', '!=', 1)
            ->lockForUpdate()
            ->firstOrFail();
            // dd('hello'); ok

            $Settlement->status = 1;
            if (!$Settlement->save()) {
                throw new \Exception('Failed to save Settlement record.');
            }
            // dd('hello');ok


            $api = Api::where('id', $Settlement->partner_id)->lockForUpdate()->firstOrFail();
            $api->balance -= $Settlement->net_amount;
            // dd('hello');ok
            if (!$api->save()) {
                throw new \Exception('Failed to save API balance update.');
            }
            // dd('hello1');ok

            $Log = new Log();
            $Log->date_time = $Settlement->created_at;
            $Log->final_amount = -$Settlement->net_amount;
            $Log->balance = $api->balance;
            $Log->transection_type = 4;
            $Log->transection_id = $Settlement->id;
            $Log->partner_id = $Settlement->partner_id;
            $Log->source = 'approveSettlement';
            if (!$Log->save()) {
                throw new \Exception('Failed to save Log entry.');
            }
            // dd('hello6');

            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $api->id)->whereDate('created_at', '>=', $Settlement->created_at)->get();
            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                $amount_to_update = $DailyPartnerSummary_record->closing_balance - $Settlement->net_amount;
                $amount_to_update = round($amount_to_update, 2);
                // $amount_to_update = floor($amount_to_update * 100) / 100;
                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                $DailyPartnerSummary_record->save();

                $summary_log = new DailyPartnerSummaryLog();
                $summary_log->partner_id = $api->id;
                $summary_log->partner_balance = $api->balance;
                $summary_log->payment_id = $Settlement->id;
                $summary_log->total_amount = -$Settlement->net_amount;
                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                $summary_log->source = 'approveSettlement';
                $summary_log->save();
            }

            DB::commit();
            session()->flash('success', 'Successfully Updated');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to Approve Settlement: ' . $e->getMessage());
            return back()->withInput();
        }


    }



    public function rejectSettlement($id)
    {
        $api = Settlement::findOrFail($id);
        $api->status = 2;
        $api->save();

        session()->flash('success', 'Successfully Updated');
        return back();
    }

    public function balanceLogs()
    {

        $accountlog = AccountLog::orderBy('id', 'DESC')->with('e_wallet_account')->paginate(10);
        $pageTitle = "Account Balance Logs";

        return view('admin.payout.balance_logs', compact('accountlog', 'pageTitle'));
    }



    public function transferBalance(Request $request)
    {
        if (!empty($request->from_date)) {
            $from_date = $request->from_date;
        } else {
            $from_date = date('Y-m-d');
        }

        $e_wallet_accounts = EWalletAccount::paginate(10);
        $e_wallet_transections = EWalletTransfer::whereDate('transaction_date_time', '=', $from_date)->orderBy('created_at', 'desc')->get();
        $pageTitle = "Transfer Logs";
        return view('admin.payout.ewallet_transfer', compact('pageTitle', 'from_date', 'e_wallet_accounts', 'e_wallet_transections'));
    }



    public function transferBalanceAdd(Request $request)
    {
        // dd($request->all());
        // dd('hello');
        $transfer_from = $request->transfer_from1;
        $transfer_to = $request->transfer_to1;

        $EWalletTransaction = new EWalletTransfer;

        if($request->category=="E-wallet to E-wallet"){
            $from_e_wallet_accounts = EWalletAccount::findOrFail($transfer_from);
            $to_e_wallet_accounts = EWalletAccount::findOrFail($transfer_to);
            $EWalletTransaction->from_e_wallet_id = $from_e_wallet_accounts->id;
            $EWalletTransaction->from_account_no = $from_e_wallet_accounts->account_no;
            $EWalletTransaction->to_e_wallet_id = $to_e_wallet_accounts->id;
            $EWalletTransaction->to_account_no = $to_e_wallet_accounts->account_no;
            $EWalletTransaction->e_wallet = $from_e_wallet_accounts->e_wallet_name;
        }elseif($request->category=="Bank to E-wallet"){
            $to_e_wallet_accounts = EWalletAccount::findOrFail($transfer_to);
            $EWalletTransaction->from_e_wallet_id = 0;
            $EWalletTransaction->from_account_no = $request->transfer_from2;
            $EWalletTransaction->to_e_wallet_id = $to_e_wallet_accounts->id;
            $EWalletTransaction->to_account_no = $to_e_wallet_accounts->account_no;
            $EWalletTransaction->e_wallet = $to_e_wallet_accounts->e_wallet_name;
        }elseif($request->category=="E-wallet to Bank"){
            $from_e_wallet_accounts = EWalletAccount::findOrFail($transfer_from);
            $EWalletTransaction->from_e_wallet_id = $from_e_wallet_accounts->id;
            $EWalletTransaction->from_account_no = $from_e_wallet_accounts->account_no;
            $EWalletTransaction->to_e_wallet_id = 0;
            $EWalletTransaction->to_account_no = $request->transfer_to2;
            $EWalletTransaction->e_wallet = $from_e_wallet_accounts->e_wallet_name;
        }

        if($EWalletTransaction->from_e_wallet_id > 0){
            $matched = 0;
            $SmsLog = SmsLog::where('e_wallet_name', $from_e_wallet_accounts->e_wallet_name)->where('txn', $request->txn_id)->where('e_wallet_no', $from_e_wallet_accounts->account_no)->orderBy('id', 'desc')->first();
            if($SmsLog){
                if($SmsLog->matched==1){
                    $matched = 1;
                }
            }

            if($matched == 0){
                $from_e_wallet_accounts->balance = $from_e_wallet_accounts->balance - $request->amount - $request->charges + $request->comission;
                $from_e_wallet_accounts->live_balance = $from_e_wallet_accounts->live_balance - $request->amount - $request->charges + $request->comission;
                $from_e_wallet_accounts->save();
            }
        }

        if($EWalletTransaction->to_e_wallet_id > 0){
            $matched = 0;
            $SmsLog = SmsLog::where('e_wallet_name', $to_e_wallet_accounts->e_wallet_name)->where('txn', $request->txn_id)->where('e_wallet_no', $to_e_wallet_accounts->account_no)->orderBy('id', 'desc')->first();
            if($SmsLog){
                if($SmsLog->matched==1){
                    $matched = 1;
                }
            }

            if($matched == 0){
                $to_e_wallet_accounts->balance = $to_e_wallet_accounts->balance + $request->amount - $request->charges + $request->comission;
                $to_e_wallet_accounts->live_balance = $to_e_wallet_accounts->live_balance + $request->amount - $request->charges + $request->comission;
                $to_e_wallet_accounts->save();
            }
        }





        $EWalletTransaction->category = $request->category;
        $EWalletTransaction->amount = $request->amount;
        $EWalletTransaction->charges = $request->charges;
        $EWalletTransaction->comission = $request->comission;
        $EWalletTransaction->txn_id = $request->txn_id;
        $EWalletTransaction->transaction_date_time = $request->transaction_date_time;



        if ($request->hasFile('image')) {

            $uploadedImage = $this->uploadImage($request->image, config('location.receipts.path'));
            $EWalletTransaction->image = $uploadedImage;
            $EWalletTransaction->save();
        }
        $EWalletTransaction->save();

        session()->flash('success', 'Added Successfully');
        return back();
    }

    public function payoutGateway()
    {

        $gateways = Gateway::where('status', 1)
            ->select('name', 'image')
            ->get();

        foreach ($gateways as $key => $gateway) {
            $data[$key]['name'] = $gateway->name;
            $data[$key]['image'] = $gateway->image ? (env('APP_URL') . config('location.withdraw.path') . $gateway->image) : '';
        }

        return $data;
    }

      public function addPayout(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'api_key' => 'required|string',
                'e_wallet_name' => 'required|string',
                'amount' => 'required',
                'user_account_no' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }


            $acc = $request->user_account_no;
            $ewalletee = strtolower($request->e_wallet_name);

            if (!is_numeric($acc)) {
                return response()->json(['code' => 605, 'error' => 'Account number formate not valid'], 404);
            }

            if (substr($acc, 0, 2) === "01") {
                $num_digits = strlen($acc);
                if ($ewalletee == 'bkash' && $num_digits != 11) {
                    return response()->json(['code' => 605, 'error' => 'Account number should be 11 digit'], 404);
                }
                if ($ewalletee == 'nagad' && $num_digits != 11) {
                    return response()->json(['code' => 605, 'error' => 'Account number should be 11 digit'], 404);
                }
                if ($ewalletee == 'rocket' && ($num_digits < 11 || $num_digits > 12)) {
                    return response()->json(['code' => 605, 'error' => 'Account number should be 11 or 12 digit'], 404);
                }
            } else {
                return response()->json(['code' => 605, 'error' => 'Account number should start from 01'], 404);
            }

            $request->amount = str_replace(',', '', $request->amount);
            $user_sign = "";

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
                            "amount" => $request->amount,
                            "api_key" => $request->api_key,
                            "e_wallet_name" => $request->e_wallet_name,
                            "user_account_no" => $request->user_account_no
                        ));
                        $user_sign = $request->sign;
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

            if ($api_key->min_withdrawal > $request->amount) {
                return response()->json(['message' => 'Min Withdrawal Limit is ' . $api_key->min_withdrawal], 404);
            }

            $partner_transection_id = 0;
            if ($request->filled('partner_transection_id')) {
                $partner_transection_id = $request->partner_transection_id;
            }

            $member_id = "";
            if ($request->filled('member_id')) {
                $member_id = $request->member_id;
            }

            $method = Gateway::where('status', 1)
                ->where('name', $request->e_wallet_name)
                ->first();

            $currentMonth = now()->format('Y-m');
            $charge = 0;

            if ($source != env('APP_WEBSITE')) {
                // $api_key->balance +=$request->amount;
                // $api_key->save();


                $sum = Payout::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->where('api_id', $api_id)
                    ->where('status', 'Complete')
                    ->sum('amount');

                if (!$sum) {
                    $sum = 0;
                }

                $commissions = Commission::where('api_id', $api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
                if ($commissions) {
                    $charge = $commissions->withdrawal_percentage * $request->amount / 100;
                } else {
                    $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
                    if ($commissions) {
                        $charge = $commissions->withdrawal_percentage * $request->amount / 100;
                    }
                }
            }

            $pending_payout_ids = Payout::where('api_id', $api_key->id)
                ->where('status', 'Pending')
                ->pluck('id');


            $previous_pending = Payout::where('api_id', $api_key->id)
                ->where(function ($query) use ($pending_payout_ids) {
                    $query->whereIn('status', [0, 1])
                        ->orWhere(function ($subQuery) use ($pending_payout_ids) {
                            $subQuery->where('status', 2)
                                    ->whereIn('id', $pending_payout_ids);
                        });
                })
                ->sum('amount');


                // $previous_pending = PayoutLog::where('api_id', $api_key->id)
                // ->whereIn('status', [0, 1])
                // ->sum('amount');


            if ($request->amount + $charge + $previous_pending > $api_key->balance) {
                return response()->json([
                    'code' => '51',
                    'status' => 'fail',
                    'message' => 'Insufficient Balance'
                ], 404);
            }

            $payout = new Payout();
            // $payout->source = $source;
            // $payout->sign = $user_sign;
            $payout->api_id = $api_id;
            $payout->e_wallet_name = $request->e_wallet_name;
            $payout->amount = $request->amount;
            $payout->user_account_no = $request->user_account_no;
            $payout->partner_transection_id = $partner_transection_id;
            if ($request->filled('partner_transection_id')) {
                $payout->partner_transection_id = $request->partner_transection_id;
            }
            if ($request->filled('member_id')) {
                $payout->member_id = $request->member_id;
            }
            $payout->save();



            if ($charge > 0 && $api_key->parent_id > 0) {
                // $parent_commissions = Commission::where('id', $commissions->parent_id)->first();
                if ($commissions->parent_id > 0 && $commissions->parent_withdrawal_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $api_key->parent_id;
                    $PartnerCommission->type = 2;
                    $PartnerCommission->amount = $request->amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $request->amount + $charge;
                    $PartnerCommission->charges_p = $commissions->withdrawal_percentage;
                    $profit_p = $commissions->parent_withdrawal_percentage;
                    $profit = $profit_p * $request->amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $payout->id;
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();

                    // $main_parent_commissions = Commission::where('id', $parent_commissions->parent_id)->first();

                }

                if ($commissions->parent2_id > 0 && $commissions->parent2_withdrawal_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $commissions->parent2_id;
                    $PartnerCommission->type = 2;
                    $PartnerCommission->amount = $request->amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $request->amount + $charge;
                    $PartnerCommission->charges_p = $commissions->withdrawal_percentage;
                    $profit_p = $commissions->parent2_withdrawal_percentage;
                    $profit = $profit_p * $request->amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $payout->id;
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }



            $payout->user_id = 0;
            $payout->gateway_id = $method->id;
            $payout->charge = $charge;
            $payout->status = 'Pending';
            $payout->user_account_no = $request->user_account_no;


            // $payout->payout_log_id = $pre_payout->id;
            $payout->charge = $charge;
            $payout->save();

            if ($api_key->acc_type == "Partner") {

                $current_time = Carbon::now('Asia/Dhaka');

                $this->updateLimits();
                $this->updateEWallets();





                    $account = EWalletAccount::where('e_wallet_name', $payout->gateway->name)
                        ->where('type', 'Agent')
                        ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                        ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$payout->amount])
                        ->where('status', 1)
                        ->where('max_withdrawal_amount', '>=', $request->amount)
                        ->whereIn('account_type', ['Withdrawal', 'Both'])
                        ->where(function ($query) use ($current_time) {
                            $query->where('apply_time_limit', 0)
                                ->orWhere(function ($query) use ($current_time) {
                                    $query->where('apply_time_limit', 1)
                                        ->where('from_time', '<=', $current_time)
                                        ->where('to_time', '>=', $current_time);
                                });
                        })
                        ->orderBy('daily_sent', 'asc')
                        ->first();
                    if (!$account) {
                        $account = EWalletAccount::where('e_wallet_name', $payout->gateway->name)
                            ->where('type', 'Merchant')
                            ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                            ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$payout->amount])
                            ->where('status', 1)
                            ->where('max_withdrawal_amount', '>=', $request->amount)
                            ->whereIn('account_type', ['Withdrawal', 'Both'])
                            ->where(function ($query) use ($current_time) {
                                $query->where('apply_time_limit', 0)
                                    ->orWhere(function ($query) use ($current_time) {
                                        $query->where('apply_time_limit', 1)
                                            ->where('from_time', '<=', $current_time)
                                            ->where('to_time', '>=', $current_time);
                                    });
                            })
                            ->orderBy('daily_sent', 'asc')
                            ->first();
                        if (!$account) {
                            $account = EWalletAccount::where('e_wallet_name', $payout->gateway->name)
                                ->where('type', 'Personal')
                                ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                                ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$payout->amount])
                                ->where('status', 1)
                                ->where('max_withdrawal_amount', '>=', $request->amount)
                                ->whereIn('account_type', ['Withdrawal', 'Both'])
                                ->where(function ($query) use ($current_time) {
                                    $query->where('apply_time_limit', 0)
                                        ->orWhere(function ($query) use ($current_time) {
                                            $query->where('apply_time_limit', 1)
                                                ->where('from_time', '<=', $current_time)
                                                ->where('to_time', '>=', $current_time);
                                        });
                                })
                                ->orderBy('daily_sent', 'asc')
                                ->first();
                        }
                    }



                if (!$account) {
                    return response()->json(['message' => 'No E-wallet account Available at this time to proceed this request.'], 404);
                }

                $payout->status = 'Approved';
                $payout->e_wallet_phone_number = $account->account_no;
                $payout->e_wallet_type = $account->type;
                $payout->save();
            }

            return response()->json(['id' => $payout->id, 'message' => 'Payout Request has been sent'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateEWallets()
    {
        $records = EWalletAccount::get();
        foreach ($records as $record) {
            $record->is_live = 0;
            $ApiHit = ApiHit::where('e_wallet_name', $record->e_wallet_name)
                ->where('acc_no', $record->account_no)
                ->whereBetween('created_at', [now()->subSeconds(70), now()])
                ->first();
            if ($ApiHit) {
                $record->is_live = 1;
            }
            $record->save();
        }
    }

    public function lastPayoutDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'partner_transaction_id' => 'filled|string',
            'e_wallet_name' => 'required_unless:partner_transaction_id,null|string',
            'amount' => 'required_unless:partner_transaction_id,null',
            'user_account_no' => 'required_unless:partner_transaction_id,null|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $request->amount = str_replace(',', '', $request->amount);

        $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->first();
        if ($api_key) {
            $source = $api_key->website;
            $secretKey = $api_key->secret_key;

            if ($api_key->sign == 1) {
                if ($request->filled('sign')) {
                    $string_to_hash = json_encode(array(
                        "amount" => $request->amount,
                        "api_key" => $request->api_key,
                        "e_wallet_name" => $request->e_wallet_name,
                        "user_account_no" => $request->user_account_no
                    ));
                    if ($request->filled('partner_transection_id')) {
                        $string_to_hash = json_encode(array(
                            "api_key" => $request->api_key,
                            "partner_transection_id" => $request->partner_transection_id
                        ));
                    }
                    // return $string_to_hash;
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

        if ($request->filled('partner_transection_id')) {
            $lastPayout = Payout::where('partner_transection_id', $request->partner_transection_id)->where('api_id', $api_key->id)
                ->latest()->first();
            if ($lastPayout) {

                if (is_null($lastPayout->member_id)) {
                    unset($lastPayout->member_id);
                }
                return response()->json($lastPayout);
            } else {
                return response()->json(['message' => 'No payout records found.'], 404);
            }
        }



        $lastPayout = Payout::where('e_wallet_name', $request->e_wallet_name)->where('api_id', $api_key->id)
            ->where('amount', $request->amount)
            ->where('user_account_no', $request->user_account_no)
            ->latest()->first();

        $lastPayout->sign = "";




        if ($lastPayout) {
            return response()->json($lastPayout);
        } else {
            return response()->json(['message' => 'No payout records found.'], 404);
        }
    }

    public function allPayoutInfo()
    {

        $allPayoutInfo = Payout::where('status', 'Pending')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->get();

        $array = [];
        $count = 0;
        foreach ($allPayoutInfo as $payout) {
            $originalName = $payout->e_wallet_name;
            $updatedName = '';

            switch (strtolower($originalName)) {
                case 'bkash':
                    $updatedName = 'bKash';
                    break;
                case 'nagad':
                    $updatedName = 'Nagad';
                    break;
                case 'rocket':
                    $updatedName = 'Rocket';
                    break;
                    // Add more cases as needed

                default:
                    // Use the original name if it doesn't match any of the cases
                    $updatedName = $originalName;
                    break;
            }

            // Update the e_wallet_name column
            $payout->update(['e_wallet_name' => $updatedName]);

            $array[$count] = $payout;
            $array[$count]['amount'] = round($payout->amount, 2);
            $array[$count]['charge'] = round($payout->charge, 2);
            $array[$count]['fee'] = round($payout->fee, 2);
            $array[$count]['e_wallet_charges'] = round($payout->e_wallet_charges, 2);
            $array[$count]['commission'] = round($payout->commission, 2);

            $count++;
        }

        if ($allPayoutInfo) {
            return response()->json($array);
        } else {
            return response()->json(['message' => 'No payout records found.'], 404);
        }
    }

    public function addPayoutInfo(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'api_key' => 'required|string',
                'id' => 'required|numeric',
                'e_wallet_name' => 'nullable|string',
                'amount' => 'nullable',
                'user_account_no' => 'nullable|string',
                'txn_id' => 'nullable|string',
                'date' => 'required',
                'time' => 'required',
                'date_time' => 'nullable',
                'transaction_type' => 'required|string',
                'e_wallet_phone_number' => 'nullable|string',
                'ip_address' => 'nullable|string',
                'e_wallet_type' => 'nullable|string',
                'mac_address' => 'nullable|string',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = Payout::where('id', $request->id)->lockForUpdate()->first();
            if (!$data) {
                DB::rollBack();
                return response()->json(['message' => 'Payout log not exist.']);
            }

            $request->amount = str_replace(',', '', $request->amount);

            if ($request->status == "Complete") {
                $payour_record = Payout::where('status', "Complete")->where('id', $request->id)->first();
                if ($payour_record) {
                    DB::rollBack();
                    return response()->json(['message' => 'Payout Already Added']);
                }
            }

            if ($request->status != "Complete" && $request->status != "Reject") {
                DB::rollBack();
                return response()->json(['message' => 'Wrong Status!']);
            }

            $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->first();
            if ($api_key && $api_key->website == env('APP_WEBSITE')) {
                $source = $api_key->website;
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Wrong API key'], 404);
            }

            $payout = Payout::where('id', $request->id)->lockForUpdate()->first();

            if ($request->filled('e_wallet_name')) {
                $payout->e_wallet_name = $request->e_wallet_name;
            }

            if ($request->filled('amount')) {
                $payout->amount = $request->amount;
            }

            if ($request->filled('user_account_no')) {
                $payout->user_account_no = $request->user_account_no;
            }

            if ($request->filled('txn_id')) {
                $payout->txn_id = $request->txn_id;
            }

            if ($request->filled('transaction_type')) {
                $payout->transaction_type = $request->transaction_type;
            }

            if ($request->filled('e_wallet_phone_number')) {
                $payout->e_wallet_phone_number = $request->e_wallet_phone_number;
            }

            if ($request->filled('e_wallet_type')) {
                $payout->e_wallet_type = $request->e_wallet_type;
            }

            if ($request->filled('mac_address')) {
                $payout->mac_address = $request->mac_address;
            }

            if ($request->filled('status')) {
                $payout->status = $request->status;
            }

            if ($request->filled('fee')) {
                $payout->fee = $request->fee;
            }

            if ($request->filled('commission')) {
                $payout->commission = $request->commission;
            }

            $payout->ip_address = $request->ip();

            if ($request->filled('date')) {
                $formattedDate = Carbon::createFromFormat('h:ia d/m/y', $request->date)->format('Y-m-d');

            }

            if ($request->filled('time')) {
                $formattedTime = Carbon::createFromFormat('h:ia d/m/y', $request->time)->format('H:i:s');

            }

            if (is_null($request->date_time)) {
                $formattedDateTime = isset($formattedDate) && isset($formattedTime) ? $formattedDate . ' ' . $formattedTime : null;
                $payout->date_time = $formattedDateTime;
            } else {
                $formattedDateTime = Carbon::parse($request->date_time)->format('Y-m-d H:i:s');
                $payout->date_time = $formattedDateTime;
            }

            $now = Carbon::now();
            $twoHoursAgo = $now->subHours(2);

            $parsedDateTime = Carbon::parse($formattedDateTime, 'Asia/Dhaka');
            if ($parsedDateTime->lessThan($twoHoursAgo)) {
                return "$formattedDateTime is less than two hours ago.";
            }

            if ($request->status == "Complete") {
                $payout->completions_at = Carbon::now();
            }
            $payout->save();
            $this->updateLimits();

            $commit = 0;
            if ($request->status == "Complete") {

                $net_amount = $payout->amount + $payout->charge;
                $api_endpoint = "";
                $partner_api_key = Api::where('id', $payout->api_id)->where('type', 'Admin')->lockForUpdate()->first();
                if ($partner_api_key) {
                    $partner_api_key->balance -= $net_amount;
                    $partner_api_key->save();
                    $api_endpoint = $partner_api_key->api_endpoint_withdrawal;

                    $Log = new Log();
                    $Log->date_time = $payout->created_at;
                    $Log->final_amount = - ($payout->amount + $payout->charge);
                    $Log->balance = $partner_api_key->balance;
                    $Log->transection_type = 2;
                    $Log->transection_id = $payout->id;
                    $Log->partner_id = $payout->api_id;
                    $Log->source = 'AddPayoutInfo';
                    $Log->save();

                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $payout->created_at)->get();
                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                        $amount_to_update = $DailyPartnerSummary_record->closing_balance - ($payout->amount + $payout->charge);
                        $amount_to_update = round($amount_to_update, 2);
                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                        $DailyPartnerSummary_record->save();

                        $summary_log = new DailyPartnerSummaryLog();
                        $summary_log->partner_id = $partner_api_key->id;
                        $summary_log->partner_balance = $partner_api_key->balance;
                        $summary_log->payment_id = $payout->id;
                        $summary_log->total_amount = - ($payout->amount + $payout->charge);
                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                        $summary_log->source = 'AddPayoutInfo';
                        $summary_log->save();
                    }
                }
                $PartnerCommissions = PartnerCommission::where('transaction_id', $payout->id)->where('type', 2)->where('status', 0)->get();
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
                    $Log->source = 'AddPayoutInfo';
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
                        $summary_log->source = 'AddPayoutInfo';
                        $summary_log->save();
                    }
                }

                $account = EWalletAccount::where('e_wallet_name', $payout->e_wallet_name)
                    ->where('account_no', $payout->e_wallet_phone_number)
                    ->lockForUpdate()
                    ->first();
                if ($account) {
                    //E-Wallet Account Log Save
                    $previous_account_balance = number_format($account->balance, 2, '.', '');

                    $account->balance -= $payout->amount;
                    $account->daily_sent += $payout->amount;
                    $account->monthly_sent += $payout->amount;
                    $account->send += $payout->amount;
                    if ($request->filled('fee')) {
                        $account->fee += $request->fee;
                    }

                    if ($request->filled('commission')) {
                        $account->commission += $request->commission;
                    }
                    $account->save();


                    $e_wallet_log_save = new EWalletLog();
                    $e_wallet_log_save->previous_balance = $previous_account_balance;
                    $e_wallet_log_save->amount = -$payout->amount;
                    $e_wallet_log_save->charge = isset($payout->fee) ? $payout->fee : 0.00;
                    $e_wallet_log_save->commission = isset($payout->commission) ? $payout->commission : 0.00;
                    $e_wallet_log_save->final_amount = (-$payout->amount - $payout->fee + $payout->commission);
                    $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                    $e_wallet_log_save->transaction_type = 2;
                    $e_wallet_log_save->transaction_id = $payout->id;
                    $e_wallet_log_save->account_id = $account->id;
                    $e_wallet_log_save->source = "addPayoutInfo";
                    $e_wallet_log_save->save();

                    $data = Payout::where('id', $payout->payout_log_id)->with('method')->first();
                    if ($data) {
                        $data->status = 2;
                        $data->save();

                        $e_wallet_charge = 0;
                        $count_payouts = Payout::where('e_wallet_name', $payout->e_wallet_name)->where('e_wallet_phone_number', $payout->e_wallet_phone_number)->where('status', 'Complete')->whereDate('date', $formattedDate)->count();
                        if ($count_payouts >= $account->free_transections_day) {
                            $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->where('from_amount', '<=', $payout->amount)->where('to_amount', '>=', $payout->amount)->first();
                            if ($e_wallet_charges) {
                                $e_wallet_charge = $e_wallet_charges->wcharges;
                                if ($e_wallet_charges->wcharges_type == 2) {
                                    $e_wallet_charge = $e_wallet_charges->wcharges * $payout->amount / 100;
                                }
                            } else {
                                $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->orderBy('to_amount', 'desc')->first();
                                if ($e_wallet_charges) {
                                    $e_wallet_charge = $e_wallet_charges->wcharges;
                                    if ($e_wallet_charges->wcharges_type == 2) {
                                        $e_wallet_charge = $e_wallet_charges->wcharges * $payout->amount / 100;
                                    }
                                }
                            }
                        }

                        $payout->e_wallet_charges = $e_wallet_charge;
                        $payout->save();


                    }
                }

                 $commit = 1;
                 DB::commit();

                if (!empty($api_endpoint) && $partner_api_key->website != env('APP_WEBSITE')) {
                    $string_to_hash = json_encode(array(
                        "amount" => strval($this->convertStringToNumber($payout->amount)),
                        "api_key" => $partner_api_key->api_key,
                        "e_wallet_name" => $payout->e_wallet_name,
                        "id" => strval($payout->id),
                        'transaction_type' => 'Withdrawal',
                        "user_account_no" => strval($payout->user_account_no),
                    ));
                    $secretKey = $partner_api_key->secret_key;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                    $timestamp = time();
                    $combined = $hmac . $timestamp;
                    $sign = base64_encode($combined);


                    $array_data = [
                                'id' => $payout->id,
                                'partner_transection_id' => $payout->partner_transection_id,
                                'transaction_type' => 'Withdrawal',
                                'e_wallet_name' => $payout->e_wallet_name,
                                'amount' => $this->convertStringToNumber($payout->amount),
                                'user_account_no' => $payout->user_account_no,
                                'txn_id' => $payout->txn_id,
                                'e_wallet_phone_number' => $payout->e_wallet_phone_number,
                                'e_wallet_type' => $payout->e_wallet_type,
                                'charges' => $this->convertStringToNumber($payout->charge),
                                'status' => $payout->status,
                                'completion_date' => $payout->date,
                                'completion_time' => $payout->time,
                                'created_at' => $payout->created_at,
                                'updated_at' => $payout->updated_at,
                                'sign' => $sign,
                    ];

                    if(!empty($payout->member_id)){
                        $array_data['member_id'] = $payout->member_id;
                    }

                    $requestData = [
                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                        'request_url' => $partner_api_key->api_endpoint_withdrawal,
                        'request_payload' => json_encode($array_data),
                        'request_headers' => json_encode([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $logId = DB::table('api_logs')->insertGetId($requestData);

                    $csrfToken = Str::random(40);
                    $responseData = [];
                    try {

                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                        ])
                            ->post($api_endpoint, $array_data);

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

            } elseif ($request->status == "Reject") {

                $payout_data = Payout::where('id', $payout->id)->lockForUpdate()->first();
                if ($payout_data) {
                    if (!empty($payout_data->api_id) && $payout_data->api_id != 0) {
                        if ($payout_data->status == "Complete") {
                            $partner_api_key = Api::where('id', $payout_data->api_id)->lockForUpdate()->first();
                            $partner_api_key->balance += ($payout_data->amount + $payout_data->charge);
                            $partner_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $payout_data->updated_at;
                            $Log->final_amount = ($payout_data->amount + $payout_data->charge);
                            $Log->balance = $partner_api_key->balance;
                            $Log->transection_type = 7;
                            $Log->transection_id = $payout_data->id;
                            $Log->partner_id = $payout_data->api_id;
                            $Log->source = 'AddPayoutInfo';
                            $Log->save();

                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $payout_data->created_at)->get();
                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($payout_data->amount + $payout_data->charge);
                                $amount_to_update = round($amount_to_update, 2);
                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                $DailyPartnerSummary_record->save();

                                $summary_log = new DailyPartnerSummaryLog();
                                $summary_log->partner_id = $partner_api_key->id;
                                $summary_log->partner_balance = $partner_api_key->balance;
                                $summary_log->payment_id = $payout_data->id;
                                $summary_log->total_amount = $payout_data->amount + $payout_data->charge;
                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                $summary_log->source = 'AddPayoutInfo';
                                $summary_log->save();
                            }


                            $PartnerCommissions = PartnerCommission::where('transaction_id', $payout_data->id)->where('type', 2)->where('status', 1)->get();
                            foreach ($PartnerCommissions as $PartnerCommission) {
                                $PartnerCommission->status = 0;
                                $PartnerCommission->save();
                                $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
                                $parent_api_key->balance -= $PartnerCommission->profit;
                                $parent_api_key->save();

                                $Log = new Log();
                                $Log->date_time = $PartnerCommission->created_at;
                                $Log->final_amount = -$PartnerCommission->profit;
                                $Log->balance = $parent_api_key->balance;
                                $Log->transection_type = 5;
                                $Log->transection_id = $PartnerCommission->id;
                                $Log->partner_id = $PartnerCommission->from_id;
                                $Log->source = 'AddPayoutInfo';
                                $Log->save();

                                $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                    $amount_to_update = $DailyPartnerSummary_record->closing_balance - ($PartnerCommission->profit);
                                    $amount_to_update = round($amount_to_update, 2);
                                    // $amount_to_update = floor($amount_to_update * 100) / 100;
                                    $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                    $DailyPartnerSummary_record->save();

                                    $summary_log = new DailyPartnerSummaryLog();
                                    $summary_log->partner_id = $parent_api_key->id;
                                    $summary_log->partner_balance = $parent_api_key->balance;
                                    $summary_log->payment_id = $PartnerCommission->id;
                                    $summary_log->total_amount = -$PartnerCommission->profit;
                                    $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                    $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                    $summary_log->source = 'AddPayoutInfo';
                                    $summary_log->save();
                                }
                            }


                            $account = EWalletAccount::where('e_wallet_name', $payout_data->e_wallet_name)
                                ->where('account_no', $payout_data->e_wallet_phone_number)
                                ->where('status', 1)
                                ->lockForUpdate()
                                ->first();
                            if ($account) {

                                //E-Wallet Account Log Save
                                $previous_account_balance = number_format($account->balance, 2, '.', '');


                                $account->balance += $payout_data->amount;
                                $account->daily_sent -= $payout_data->amount;
                                $account->monthly_sent -= $payout_data->amount;
                                $account->send -= $payout_data->amount;
                                $account->save();

                                $e_wallet_log_save = new EWalletLog();
                                $e_wallet_log_save->previous_balance = $previous_account_balance;
                                $e_wallet_log_save->amount = $payout_data->amount;
                                $e_wallet_log_save->charge = isset($payout_data->fee) ? $payout_data->fee : 0.00;
                                $e_wallet_log_save->commission = isset($payout_data->commission) ? $payout_data->commission : 0.00;
                                $e_wallet_log_save->final_amount = ($payout_data->amount + $payout_data->fee - $payout_data->commission  );
                                $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                                $e_wallet_log_save->transaction_type = 4;
                                $e_wallet_log_save->transaction_id = $payout_data->id;
                                $e_wallet_log_save->account_id = $account->id;
                                $e_wallet_log_save->source = "addPayoutInfo";
                                $e_wallet_log_save->save();
                            }
                        }
                    }
                    $payout_data->status = "Reject";
                    $payout_data->save();
                }

                $data = Payout::where('id', $payout->payout_log_id)->where('status' , '!=' , 3)->with('user', 'method')->first();
                if ($data) {
                    $data->status = 3;
                    $data->save();

                    $user = $data->user;
                    $user->balance += $data->net_amount;
                    $user->save();

                    $basic = (object) config('basic');
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount = getAmount($data->net_amount);
                    $transaction->final_balance = $user->balance;
                    $transaction->charge = $data->charge;
                    $transaction->trx_type = '+';
                    $transaction->remarks = getAmount($data->amount) . ' ' . $basic->currency . ' withdraw amount has been refunded';
                    if (isset($data->trx_id) && !empty($data->trx_id)) {
                        $transaction->trx_id = $data->trx_id;
                    }
                    $transaction->save();

                    $api_endpoint = "";
                    $partner_api_key = Api::where('id', $payout->api_id)->where('type', 'Admin')->first();
                    if ($partner_api_key) {
                        $api_endpoint = $partner_api_key->api_endpoint_withdrawal;
                    }


                    $commit = 1;
                    DB::commit();


                    if (!empty($api_endpoint) && $partner_api_key->website != env('APP_WEBSITE')) {

                        $string_to_hash = json_encode(array(
                            "amount" => strval($this->convertStringToNumber($payout->amount)),
                            "api_key" => $partner_api_key->api_key,
                            "e_wallet_name" => $payout->e_wallet_name,
                            "id" => strval($payout->id),
                            'transaction_type' => 'Withdrawal',
                            "user_account_no" => strval($payout->user_account_no),
                        ));
                        $secretKey = $partner_api_key->secret_key;
                        $hash = hash("sha256", $string_to_hash);
                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                        $timestamp = time();
                        $combined = $hmac . $timestamp;
                        $sign = base64_encode($combined);

                        $array_data = [
                                    'id' => $payout->id,
                                    'partner_transection_id' => $payout->partner_transection_id,
                                    'transaction_type' => 'Withdrawal',
                                    'e_wallet_name' => $payout->e_wallet_name,
                                    'amount' => $this->convertStringToNumber($payout->amount),
                                    'user_account_no' => $payout->user_account_no,
                                    'txn_id' => $payout->txn_id,
                                    'e_wallet_phone_number' => $payout->e_wallet_phone_number,
                                    'e_wallet_type' => $payout->e_wallet_type,
                                    'charges' => $this->convertStringToNumber($payout->charge),
                                    'status' => $payout->status,
                                    'completion_date' => $payout->date,
                                    'completion_time' => $payout->time,
                                    'created_at' => $payout->created_at,
                                    'updated_at' => $payout->updated_at,
                                    'sign' => $sign,
                        ];

                        if(!empty($payout->member_id)){
                            $array_data['member_id'] = $payout->member_id;
                        }

                        $requestData = [
                            'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                            'request_url' => $partner_api_key->api_endpoint_withdrawal,
                            'request_payload' => json_encode($array_data),
                            'request_headers' => json_encode([
                                'Content-Type' => 'application/json',
                                'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $logId = DB::table('api_logs')->insertGetId($requestData);

                        $csrfToken = Str::random(40);
                        $responseData = [];
                        try {

                            $response = Http::withHeaders([
                                'Content-Type' => 'application/json',
                                'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                            ])
                                ->post($api_endpoint, $array_data);

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
                    ];
                    $action = [
                        "link" => '#',
                        "icon" => "fa fa-money-bill-alt "
                    ];

                }

                if($commit==0){
                    DB::commit();
                }
            }

            if($commit==0){
                DB::commit();
            }
            return response()->json(['message' => 'Payout information updated successfully'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function rejectPayoutInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->first();
        if ($api_key && $api_key->website == env('APP_WEBSITE')) {
            $source = $api_key->website;
        } else {
            return response()->json(['message' => 'Wrong API key'], 404);
        }

        DB::beginTransaction();
        try {
            $payout = Payout::where('id', $request->id)->lockForUpdate()->first();
            if ($payout->try < 2) {
                $payout->try = $payout->try + 1;
                $payout->save();
                DB::commit();
                return response()->json(['message' => 'Payout Tried ' . $payout->try . ' out of 3.'], 200);
            } else {
                $payout->try = $payout->try + 1;
                $payout->status = "Reject";
                $payout->save();

                $payout_data = Payout::where('id', $payout->id)->first();
                if ($payout_data) {

                    if (!empty($payout_data->api_id) && $payout_data->api_id != 0) {
                        if ($payout_data->status == "Complete") {
                            $partner_api_key = Api::where('id', $payout_data->api_id)->lockForUpdate()->first();
                            $partner_api_key->balance += ($payout_data->amount + $payout_data->charge);
                            $partner_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $payout_data->updated_at;
                            $Log->final_amount = ($payout_data->amount + $payout_data->charge);
                            $Log->balance = $partner_api_key->balance;
                            $Log->transection_type = 7;
                            $Log->transection_id = $payout_data->id;
                            $Log->partner_id = $payout_data->api_id;
                            $Log->source = 'RejectPayoutInfo';
                            $Log->save();

                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $payout_data->created_at)->get();
                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($payout_data->amount + $payout_data->charge);
                                $amount_to_update = round($amount_to_update, 2);
                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                $DailyPartnerSummary_record->save();

                                $summary_log = new DailyPartnerSummaryLog();
                                $summary_log->partner_id = $partner_api_key->id;
                                $summary_log->partner_balance = $partner_api_key->balance;
                                $summary_log->payment_id = $payout_data->id;
                                $summary_log->total_amount = $payout_data->amount + $payout_data->charge;
                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                $summary_log->source = 'RejectPayoutInfo';
                                $summary_log->save();
                            }


                            $PartnerCommissions = PartnerCommission::where('transaction_id', $payout_data->id)->where('type', 2)->where('status', 1)->get();
                            foreach ($PartnerCommissions as $PartnerCommission) {
                                $PartnerCommission->status = 0;
                                $PartnerCommission->save();
                                $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
                                $parent_api_key->balance -= $PartnerCommission->profit;
                                $parent_api_key->save();

                                $Log = new Log();
                                $Log->date_time = $PartnerCommission->created_at;
                                $Log->final_amount = -$PartnerCommission->profit;
                                $Log->balance = $parent_api_key->balance;
                                $Log->transection_type = 5;
                                $Log->transection_id = $PartnerCommission->id;
                                $Log->partner_id = $PartnerCommission->from_id;
                                $Log->source = 'RejectPayoutInfo';
                                $Log->save();

                                $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                    $amount_to_update = $DailyPartnerSummary_record->closing_balance - ($PartnerCommission->profit);
                                    $amount_to_update = round($amount_to_update, 2);
                                    // $amount_to_update = floor($amount_to_update * 100) / 100;
                                    $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                    $DailyPartnerSummary_record->save();

                                    $summary_log = new DailyPartnerSummaryLog();
                                    $summary_log->partner_id = $parent_api_key->id;
                                    $summary_log->partner_balance = $parent_api_key->balance;
                                    $summary_log->payment_id = $PartnerCommission->id;
                                    $summary_log->total_amount = -$PartnerCommission->profit;
                                    $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                    $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                    $summary_log->source = 'RejectPayoutInfo';
                                    $summary_log->save();
                                }
                            }

                            $account = EWalletAccount::where('e_wallet_name', $payout_data->e_wallet_name)
                                ->where('account_no', $payout_data->e_wallet_phone_number)
                                ->where('status', 1)
                                ->lockForUpdate()
                                ->first();
                            if ($account) {

                                //E-Wallet Account Log Save
                                $previous_account_balance = number_format($account->balance, 2, '.', '');

                                $account->balance += $payout_data->amount;
                                $account->daily_sent -= $payout_data->amount;
                                $account->monthly_sent -= $payout_data->amount;
                                $account->send -= $payout_data->amount;
                                $account->save();


                                $e_wallet_log_save = new EWalletLog();
                                $e_wallet_log_save->previous_balance = $previous_account_balance;
                                $e_wallet_log_save->amount = $payout_data->amount;
                                $e_wallet_log_save->charge = isset($payout_data->fee) ? $payout_data->fee : 0.00;
                                $e_wallet_log_save->commission = isset($payout_data->commission) ? $payout_data->commission : 0.00;
                                $e_wallet_log_save->final_amount = ($payout_data->amount + $payout_data->fee - $payout_data->commission  );
                                $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                                $e_wallet_log_save->transaction_type = 4;
                                $e_wallet_log_save->transaction_id = $payout_data->id;
                                $e_wallet_log_save->account_id = $account->id;
                                $e_wallet_log_save->source = "rejectPayoutInfo";
                                $e_wallet_log_save->save();
                            }
                        }
                    }

                    $payout_data->status = "Reject";
                    $payout_data->save();
                }

                $commit = 0;

                $data = Payout::where('id', $payout->id)->where('status', '!=' , 3)->with('user', 'gateway')->first();
                $net_amount = $data->amount + $data->charge;
                // return $data->user;
                if ($data) {

                    $data->status = 3;
                    $data->save();

                    $user = $data->user;
                    $user->balance += $net_amount;
                    $user->save();

                    $basic = (object) config('basic');

                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount = getAmount($data->net_amount);
                    $transaction->final_balance = $user->balance;
                    $transaction->charge = $data->charge;
                    $transaction->trx_type = '+';
                    $transaction->remarks = getAmount($data->amount) . ' ' . $basic->currency . ' withdraw amount has been refunded';
                    $transaction->trx_id = empty($data->trx_id) ? 'null' : $data->trx_id;
                    $transaction->save();

                    $commit = 1;
                    DB::commit();

                    $api_endpoint = "";
                    $partner_api_key = Api::where('id', $payout->api_id)->where('type', 'Admin')->first();
                    if ($partner_api_key) {
                        $api_endpoint = $partner_api_key->api_endpoint_withdrawal;
                        if (!empty($partner_api_key->api_endpoint_withdrawal) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($payout->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $payout->e_wallet_name,
                                "id" => strval($payout->id),
                                'transaction_type' => 'Withdrawal',
                                "user_account_no" => strval($payout->user_account_no),
                            ));
                            $secretKey = $partner_api_key->secret_key;
                            $hash = hash("sha256", $string_to_hash);
                            $hmac = hash_hmac('sha256', $hash, $secretKey);
                            $timestamp = time();
                            $combined = $hmac . $timestamp;
                            $sign = base64_encode($combined);

                            $array_data = [
                                        'id' => $payout->id,
                                        'partner_transection_id' => $payout->partner_transection_id,
                                        'transaction_type' => 'Withdrawal',
                                        'e_wallet_name' => $payout->e_wallet_name,
                                        'amount' => $this->convertStringToNumber($payout->amount),
                                        'user_account_no' => $payout->user_account_no,
                                        'txn_id' => $payout->txn_id,
                                        'e_wallet_phone_number' => $payout->e_wallet_phone_number,
                                        'e_wallet_type' => $payout->e_wallet_type,
                                        'charges' => $this->convertStringToNumber($payout->charge),
                                        'status' => $payout->status,
                                        'completion_date' => $payout->date,
                                        'completion_time' => $payout->time,
                                        'created_at' => $payout->created_at,
                                        'updated_at' => $payout->updated_at,
                                        'sign' => $sign,
                            ];

                            if(!empty($payout->member_id)){
                                $array_data['member_id'] = $payout->member_id;
                            }


                            $requestData = [
                                'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                'request_url' => $partner_api_key->api_endpoint_withdrawal,
                                'request_payload' => json_encode($array_data),
                                'request_headers' => json_encode([
                                    'Content-Type' => 'application/json',
                                    'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                                ]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $logId = DB::table('api_logs')->insertGetId($requestData);

                            $csrfToken = Str::random(40);
                            $responseData = [];
                            try {
                                $response = Http::withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                ])
                                    ->post($partner_api_key->api_endpoint_withdrawal, $array_data);

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
                    }
                }
            }

            if($commit==0){
                DB::commit();
            }
            return response()->json(['message' => 'Payout Rejected'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
  public function convertStringToNumber($string)
    {
        if (strpos($string, '.') !== false) {
            return (float)$string;
        } else {
            return (int)$string;
        }
    }


    public function workboard(Request $request)
    {
        $pageTitle = "Workboard";
        return view('admin.payout.workboard', compact( 'pageTitle'));
    }





}
