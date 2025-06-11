<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Log;
use App\Models\Fund;
use App\Models\Group;
use App\Models\ApiHit;
use App\Models\ApiLog;
use App\Models\Payout;
use App\Models\SmsLog;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Category;
use App\Models\CCategory;
use App\Models\Signature;
use App\Models\AccountLog;
use App\Models\Adjustment;
use App\Models\Commission;
use App\Models\EWalletLog;
use App\Models\Settlement;
use App\Http\Traits\Upload;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\AccountGroup;
// rehan
use App\Models\AdminAccount;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Models\EWalletCharge;
use App\Models\AccountGateway;
use App\Models\ApiTransaction;
use App\Models\CronCommission;
use App\Models\EWalletAccount;
use App\Models\PendingPayment;
use App\Models\EWalletTransfer;
use Illuminate\Validation\Rule;
use App\Models\ParentCommission;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use App\Models\DailyPartnerSummary;
use App\Models\TwoStepVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantReportExport;
use App\Models\DailyPartnerSummaryLog;
use App\Models\EWalletAccountTimeSlot;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Session;
use App\Exports\PartnerCommissionExport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log as LaravelLog;


class PayoutRecordController extends Controller
{
    use Upload;
    public function inlineUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:apis,id',
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);

        $allowedFields = [
            'website',
            'api_endpoint_deposit',
            'api_endpoint_withdrawal',
            'redirect_url',
            'min_deposit',
            'min_withdrawal'
        ];

        if (!in_array($request->field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field']);
        }

        $api = Api::findOrFail($request->id);
        $api->{$request->field} = $request->value;
        $api->save();

        return response()->json(['success' => true]);
    }


    public function eWalletAccounts(Request $request)
    {
        $this->updateLimits();
        $records = EWalletAccount::get();
        $accounts = AdminAccount::get();
        $nagad_accounts = AdminAccount::where('e_wallet_name', 'Nagad')->get();
        $rocket_accounts = AdminAccount::where('e_wallet_name', 'Rocket')->get();
        $bkash_accounts = AdminAccount::where('e_wallet_name', 'bKash')->get();
        foreach ($records as $record) {
            $record->live = 0;
            $ApiHit = ApiHit::where('e_wallet_name', $record->e_wallet_name)
                ->where('acc_no', $record->account_no)
                ->whereBetween('created_at', [now()->subSeconds(70), now()])
                ->first();
            if ($ApiHit) {
                $record->live = 1;
            }
        }
        // $pageTitle = "All Accounts";
        $pageTitle = __('accounts.all_accounts');
        return view('admin.payout.ewallet_accounts', compact('records', 'pageTitle', 'accounts'));
    }

    public function exportprofile($id)
    {
        $api = Api::findOrFail($id);

        $data[] = [
            'Name',
            'Username',
            'Email',
            'Phone',
            'Status',
            'Website',
            'api_endpoint_deposit',
            'api_endpoint_withdrawal',
            'type',
            'api_key',
            'last_login',
            'balance',
            'min_deposit',
            'min_withdrawal',
            'acc_type',
            'sign',
            'secret_key',
            'txn_verification',
            'redirect_url'
        ];

        $data[] = [
            $api->name,
            $api->username,
            $api->email,
            $api->phone,
            $api->status,
            $api->website,
            $api->api_endpoint_deposit,
            $api->api_endpoint_withdrawal,
            $api->type,
            $api->api_key,
            $api->last_login,
            $api->balance,
            $api->min_deposit,
            $api->min_withdrawal,
            $api->acc_type,
            $api->sign,
            $api->secret_key,
            $api->txn_verification,
            $api->redirect_url
        ];

        $currentDateTime = date('d_F_Y_h_i_A');
        $csvFileName = "api_profile_export_$currentDateTime.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function generatePassword($id)
    {
        $api = Api::findOrFail($id);
        $newPassword = Str::random(12); // 12-digit alphanumeric password

        $api->password = Hash::make($newPassword); // If it's hashed
        $api->password_string = $newPassword; // If it's hashed
        $api->save();

        return response()->json([
            'password' => $newPassword
        ]);
    }

    public function toggleStatus($id)
    {
        $eWalletAccount = EWalletAccount::findOrFail($id);
        $eWalletAccount->status = !$eWalletAccount->status;
        $eWalletAccount->save();

        if($eWalletAccount->status==1){

            $Setting = Setting::where('name', 'last_account_active')->first();
            $Setting->value = Carbon::now();
            $Setting->save();
        }


        return redirect()->back()->with('success', 'Status toggled successfully');
    }

    public function adminAccountDelete($id)
    {
        $account = AdminAccount::findOrFail($id);
        $account->delete();
        return back();
    }

    public function depositTest(Request $request)
    {
        // $form_data = $request->all();
        //

        $this->updateLimits();
        $account = EWalletAccount::where('id', $request->account_id)
            ->first();
        if (!$account) {
            return response()->json(['error' => 'You Can not Proceed With this E-wallet account'], 422);
        }

        $gate = Gateway::where('code', $request->gateway)->where('status', 1)->first();
        $charge = 0;
        $e_wallet_phone_number = $account->account_no;

        $fund = new Fund();
        $fund->user_id = 0;
        $fund->gateway_id = $gate->id;
        $fund->gateway_currency = strtoupper($gate->currency);
        $fund->amount = $request->amount;
        $fund->charge = $charge;
        $fund->account_no = $request->account_no;
        $fund->rate = $gate->convention_rate;
        $fund->final_amount = getAmount($request->amount);
        $fund->btc_amount = 0;
        $fund->btc_wallet = "";
        $fund->transaction = strRandom();
        $fund->try = 0;
        $fund->status = 2;
        $fund->e_wallet_phone_number = $e_wallet_phone_number;
        $fund->source = "Admin Test";
        $fund->save();

        $data = [
            'orderid' => $fund['id'],
            'result' => 'process',
        ];
        return response()->json($data);
    }

    public function eWalletAccountsAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'acc_no' => 'required|string',
            'e_wallet_name' => 'required|string',
        ]);

        $account = new AdminAccount;
        $account->acc_no = $request->acc_no;
        $account->e_wallet_name = $request->e_wallet_name;
        $account->save();
        session()->flash('success', 'Added Successfully');
        return back();
    }

    public function depositTestp(Request $request)
    {
        // Check if a payment already exists with this transaction_id
        $payment = Payment::where('transaction_id', $request->orderid)
            ->orderBy('id', 'DESC')
            ->first();

        if ($payment) {
            return response()->json(['result' => 'success']);
        }

        // If not, fetch the original unpaid record using orderid (as the identifier)
        $existing = Payment::where('id', $request->orderid)->first();

        if (!$existing) {
            return response()->json(['result' => 'fail']);
        }

        // Now search for a matching pending payment within 30 minutes
        $match = Payment::where('e_wallet_name', $request->gateway)
            ->where('amount', $existing->amount)
            ->where('sender', $existing->sender)
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->where('status', 'Pending')
            ->orderBy('id', 'DESC')
            ->first();

        if ($match) {
            $match->charge = 0;
            $match->status = 'Complete';
            $match->transaction_id = $request->orderid;
            $match->save();

            return response()->json(['result' => 'success']);
        }

        return response()->json(['result' => 'fail']);
    }

    public function withdrawalTest(Request $request)
    {
        $method = Gateway::where('status', 1)
            ->where('name', $request->gateway)
            ->first();

        if (!$method) {
            return response()->json(['error' => 'Invalid gateway selected'], 422);
        }

        $currentMonth = now()->format('Y-m');
        $charge = 0;

        $payout = new Payout();
        $payout->e_wallet_name = $request->gateway;
        $payout->amount = $request->amount;
        $payout->user_account_no = $request->account_no;
        $payout->charge = $charge;
        $payout->save();

        $this->updateLimits();

        $account = EWalletAccount::where('id', $request->account_id)->first();
        if (!$account) {
            return response()->json(['error' => 'You cannot proceed with this E-wallet account'], 422);
        }

        $payout->e_wallet_phone_number = $account->account_no;
        $payout->e_wallet_type = $account->type;
        $payout->status = 'Pending';
        $payout->save();

        $data = [
            'orderid' => $payout->id,
            'result' => 'process',
        ];

        return response()->json($data);
    }


    public function withdrawalTestp(Request $request)
    {
        // $form_data = $request->all();

        $payour_record = Payout::where('status', "Complete")->where('id', $request->wid)->first();
        if ($payour_record) {
            $data = [
                'result' => 'success',
            ];
            return response()->json($data);
        }

        $data = [
            'result' => 'fail',
        ];
        return response()->json($data);
    }


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
            $status = 'Pending';
        } elseif ($status == "Complete") {
            $status = 'Complete';
        } else {
            $status = "";
        }

        // dd($status);

        $records = Payout::where('status', 'like', '%' . $status . '%')
            ->where('status', '!=', 'initiate')
            ->with('user', 'gateway')
            ->whereDate('created_at', $date) // Moved here directly
            ->where('e_wallet_name', 'like', '%' . $gateway . '%') // Moved here directly
            ->orderBy('id', 'DESC')
            ->paginate(config('basic.paginate'));


        $funds_t = Payout::where('status', '!=', 'initiate')
            ->where('status', 'like', '%' . $status . '%')
            ->whereDate('created_at', $date) // Applied directly
            ->where('e_wallet_name', 'like', '%' . $gateway . '%') // Applied directly
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
            ->with('user', 'gateway') // Removed 'payout'
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
        $from_date = date('Y-m-d');
        return view('admin.payout.report', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum', 'from_date'));
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
                    return $query->where('transfer_status', 2)->where('status', 'Complete');
                } else {
                    return $query->where('status', $search['status']);
                }
            })
            ->when(isset($search['domain']), function ($query) use ($search) {
                return $query->where('api_id', $search['domain']);
            })
            ->where('transfer_status', '!=', 0)
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
        $from_date = date('Y-m-d');
        return view('admin.payout.report', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum','from_date'));
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
                    $query->where('transfer_status', $search['status']);
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

                $data[] = [
                    $item->created_at,
                    $item->trx_id,
                    $item->txn_id,
                    $item->partner_transection_id,
                    $user_name,
                    $user_type,
                    $item->e_wallet_name,
                    $item->user_account_no,
                    getAmount($item->amount),
                    $item->charge,
                    getAmount($item->amount + $item->charge),
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
                    $query->where('transfer_status', $search['status']);
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
        $records = Payout::where('status', '!=', 'initiate')->orderBy('id', 'DESC')->with('user', 'gateway', 'api')->paginate(config('basic.paginate'));
        return view('admin.payout.logs', compact('records', 'pageTitle', 'domains', 'letest_record'));
    }

        public function export_by_logs_for_WithDrawl($from_date)
    {
        $from_date = str_replace('/', '', $from_date); // Remove any slashes if present

        try {
            $sanitizedDate = Carbon::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
        return Excel::download(new MerchantReportExport($from_date), "merchant_report_by_date_{$sanitizedDate}.csv");
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
            $data = Payout::where('id', $request->id)->whereIn('transfer_status', [1, 2])->with('user', 'gateway')->lockForUpdate()->first();
            // 1 in pending // 2 success
            $basic = (object) config('basic');

            $commit = 0;

            //approved
            if ($request->status == '2') {
                if (strtolower($data->gateway->name) == "nagad" || strtolower($data->gateway->name) == "rocket" || strtolower($data->gateway->name) == "bkash") {
                    //  $result = $this->checkPayoutAmountWithinTime($data);
                    $this->updateLimits();
                    $this->updateEWallets();

                    $Setting = Setting::where('name', 'last_account_active')->first();

                    $now = Carbon::now();
                    $startOfToday = $now->copy()->startOfDay();
                    $startOfMonth = $now->copy()->startOfMonth();
                    $oneMinuteAgo = $now->copy()->subMinute();

                    // Query
                    $results = Payout::selectRaw('
                            e_wallet_phone_number,
                            COUNT(CASE WHEN created_at >= ? THEN 1 END) AS counts_for_round_robin,
                            COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS today_count,
                            COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS month_count,
                            COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS one_min_count,
                            SUM(CASE WHEN completions_at >= ? AND status = "Complete" THEN amount ELSE 0 END) AS one_min_sum
                        ', [
                            $Setting->value,
                            $startOfToday,
                            $startOfMonth,
                            $oneMinuteAgo,
                            $oneMinuteAgo
                        ])
                        ->where('e_wallet_name', $data->gateway->name)
                        ->whereNotNull('e_wallet_phone_number')
                        ->where('e_wallet_phone_number', '!=', '')
                        ->groupBy('e_wallet_phone_number')
                        ->get();


                        // ->where('created_at', '>=', $startOfMonth)

                        $all_accounts = [];

                        foreach($results as $result){
                            $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
                            $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
                            $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
                            $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
                            $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
                        }

                    $current_time = Carbon::now('Asia/Dhaka');
                    $account = EWalletAccount::where('e_wallet_name', $data->gateway->name)
                        ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                        ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$data->amount])
                        ->where('status', 1)
                        ->where('max_withdrawal_amount', '>=', $data->amount)
                        ->whereIn('account_type', ['Withdrawal', 'Both'])
                        ->with('timeSlots')
                        ->get()
                        ->filter(function ($single_account) use ($all_accounts, $current_time) {
                            $phone = $single_account->account_no;

                            // Check all transaction limits
                            $validTransactionLimits = !isset($all_accounts[$phone]) || (
                                $single_account->daily_limit_transaction > ($all_accounts[$phone]['today_count'] ?? 0) &&
                                $single_account->monthly_limit_transaction > ($all_accounts[$phone]['month_count'] ?? 0) &&
                                $single_account->max_transaction_per_minute > ($all_accounts[$phone]['one_min_count'] ?? 0) &&
                                $single_account->max_amount_per_minute > ($all_accounts[$phone]['one_min_sum'] ?? 0)
                            );

                            // Check if at least one time slot matches

                            $validTimeSlot = $single_account->timeSlots->contains(function ($slot) use ($current_time) {
                                $from = Carbon::parse($slot->from_time);
                                $to = Carbon::parse($slot->to_time);

                                return $current_time->between($from, $to);
                            });


                            return $validTransactionLimits && $validTimeSlot;
                        })
                        ->sortBy(function ($single_account) use ($all_accounts) {
                            return $all_accounts[$single_account->account_no]['counts_for_round_robin'] ?? 0;
                        })
                        ->values()
                        ->first();


                    if (!$account) {
                        DB::rollBack();
                        throw new \Exception("No E-wallet account Available at this time to proceed this request.");
                    }

                    if (isset($data->information->PhoneNumber->field_name)) {
                        $user_account_no =  $data->information->PhoneNumber->field_name;
                    } else {
                        $user_account_no =  $data->user_account_no;
                    }

                    $data->api_id = $data->api_id;
                    $data->e_wallet_name = $data->gateway->name;
                    $data->amount = $data->amount;
                    $data->user_account_no = $user_account_no;
                    $data->e_wallet_phone_number = $account->account_no;
                    $data->e_wallet_type = $account->type;
                    $data->status = 'Pending';
                    $data->feedback = $request->feedback;
                    $data->save();
                }


                $partner_api_key = Api::where('id', $data->api_id)->firstOrFail();

                $sum = Payout::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->where('api_id', $partner_api_key->id)
                    ->where('status', 'Complete')
                    ->sum('amount');

                if (!$sum) {
                    $sum = 0;
                }

                $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($commissions) {
                    $charge = $commissions->withdrawal_percentage * $data->amount / 100;
                } else {
                    $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($commissions) {
                        $charge = $commissions->withdrawal_percentage * $data->amount / 100;
                    }
                }


                $parentIds = ParentCommission::where('user_id', $partner_api_key->id)
                    ->pluck('parent_id')
                    ->unique()
                    ->values();
                foreach ($parentIds as  $parentId) {

                    $parent_charge = 0;

                    $parent_commission = ParentCommission::where('user_id', $partner_api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->withdrawal_percentage * $data->amount / 100;
                    } else {
                        $parent_commission = ParentCommission::where('user_id', $partner_api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                        if ($parent_commission) {
                            $parent_charge = $parent_commission->withdrawal_percentage * $data->amount / 100;
                        }
                    }

                    if ($parent_charge > 0) {
                        $PartnerCommission = new PartnerCommission();
                        $PartnerCommission->api_id = $partner_api_key->id;
                        $PartnerCommission->from_id = $parentId;
                        $PartnerCommission->type = 2;
                        $PartnerCommission->amount = $data->amount;
                        $PartnerCommission->charges = $charge;
                        $PartnerCommission->total_amount =  $data->amount + $charge;
                        $PartnerCommission->charges_p = $commissions->withdrawal_percentage ?? 0;
                        $profit_p = $parent_commission->withdrawal_percentage;
                        $profit = $profit_p * $data->amount / 100;
                        $PartnerCommission->profit = $profit;
                        $PartnerCommission->profit_p = $profit_p;
                        $PartnerCommission->transaction_id = $data->id;
                        $PartnerCommission->status = 0;
                        $PartnerCommission->save();
                    }
                }

                $data->transfer_status = 2;
                $data->feedback = $request->feedback;
                $data->save();

                $commit = 1;
                DB::commit();

                //$user = $data->user;

                session()->flash('success', 'Approve Successfully');
            } elseif ($request->status == 3) {

                if ($data->transfer_status == 3) {
                    DB::rollBack();
                    throw new \Exception("This transaction already rejected!.");
                }

                $data->transfer_status = 3;
                $data->feedback = $request->feedback;
                $data->save();

                if ($data->status == "Complete") {
                    $partner_api_key = Api::where('id', $data->api_id)->lockForUpdate()->firstOrFail();
                    $partner_api_key->balance += ($data->amount + $data->charge);
                    $partner_api_key->save();

                    $Log = new Log();
                    $Log->date_time = $data->updated_at;
                    $Log->final_amount = ($data->amount + $data->charge);
                    $Log->balance = $partner_api_key->balance;
                    $Log->transection_type = 7;
                    $Log->transection_id = $data->id;
                    $Log->partner_id = $data->api_id;
                    $Log->source = 'AdminPanel';
                    $Log->save();

                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $data->created_at)->get();
                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($data->amount + $data->charge);
                        $amount_to_update = round($amount_to_update, 2);
                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                        $DailyPartnerSummary_record->save();

                        $summary_log = new DailyPartnerSummaryLog();
                        $summary_log->partner_id = $partner_api_key->id;
                        $summary_log->partner_balance = $partner_api_key->balance;
                        $summary_log->payment_id = $data->id;
                        $summary_log->total_amount = $data->amount + $data->charge;
                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                        $summary_log->source = 'AdminPanel';
                        $summary_log->save();
                    }

                    $PartnerCommissions = PartnerCommission::where('transaction_id', $data->id)->where('type', 2)->where('status', 1)->get();
                    foreach ($PartnerCommissions as $PartnerCommission) {
                        $PartnerCommission->status = 0;
                        $PartnerCommission->save();
                        $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->firstOrFail();
                        if($parent_api_key){
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

                    }


                    $account = EWalletAccount::where('e_wallet_name', $data->e_wallet_name)
                        ->where('account_no', $data->e_wallet_phone_number)
                        ->where('status', 1)
                        ->lockForUpdate()->firstOrFail();
                    if ($account) {
                        //E-Wallet Account Log Save
                        $previous_account_balance = number_format($account->balance, 2, '.', '');

                        $account->balance += $data->amount;
                        $account->daily_sent -= $data->amount;
                        $account->monthly_sent -= $data->amount;
                        $account->send -= $data->amount;
                        $account->save();

                        $e_wallet_log_save = new EWalletLog();
                        $e_wallet_log_save->previous_balance = $previous_account_balance;
                        $e_wallet_log_save->amount = $data->amount;
                        $e_wallet_log_save->charge = isset($data->fee) ? $data->fee : 0.00;
                        $e_wallet_log_save->commission = isset($data->commission) ? $data->commission : 0.00;

                        $e_wallet_log_save->final_amount = ($data->amount + $data->fee - $data->commission);
                        $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                        $e_wallet_log_save->transaction_type = 4;
                        $e_wallet_log_save->transaction_id = $data->id;
                        $e_wallet_log_save->account_id = $account->id;
                        $e_wallet_log_save->source = "action";
                        $e_wallet_log_save->save();
                    }
                }

                if ($data) {
                    $data->status = "Reject";
                    $data->save();
                }


                $commit = 1;
                DB::commit();

                $api_endpoint = "";
                $partner_api_key = Api::where('id', $data->api_id)->where('type', 'Admin')->lockForUpdate()->first();
                if ($partner_api_key) {
                    $api_endpoint = $partner_api_key->api_endpoint_withdrawal;
                    if (!empty($partner_api_key->api_endpoint_withdrawal) && $partner_api_key->website != env('APP_WEBSITE')) {

                        $string_to_hash = json_encode(array(
                            "amount" => strval($this->convertStringToNumber($data->amount)),
                            "api_key" => $partner_api_key->api_key,
                            "e_wallet_name" => $data->e_wallet_name,
                            "id" => strval($data->id),
                            'transaction_type' => 'Withdrawal',
                            "user_account_no" => strval($data->user_account_no),
                        ));
                        $secretKey = $partner_api_key->secret_key;
                        $hash = hash("sha256", $string_to_hash);
                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                        $timestamp = time();
                        $combined = $hmac . $timestamp;
                        $sign = base64_encode($combined);

                        $datetime = Carbon::parse($data->date_time);
                        $api_date = $datetime->toDateString();   // '2025-05-19'
                        $api_time = $datetime->toTimeString();   // '15:43:00'

                        $array_data = [
                            'id' => $data->id,
                            'partner_transection_id' => $data->partner_transection_id,
                            'transaction_type' => 'Withdrawal',
                            'e_wallet_name' => $data->e_wallet_name,
                            'amount' => $this->convertStringToNumber($data->amount),
                            'user_account_no' => $data->user_account_no,
                            'txn_id' => $data->txn_id,
                            'e_wallet_phone_number' => $data->e_wallet_phone_number,
                            'e_wallet_type' => $data->e_wallet_type,
                            'charges' => $this->convertStringToNumber($data->charge),
                            'status' => $data->status,
                            'completion_date' => $api_date,
                            'completion_time' => $api_time,
                            'created_at' => $data->created_at,
                            'updated_at' => $data->updated_at,
                            'sign' => $sign,
                            'remarks' => $request->feedback,
                        ];

                        if (!empty($data->member_id)) {
                            $array_data['member_id'] = $data->member_id;
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
            } elseif ($request->status == 4) {
                $this->updateLimits();

                if ($data->status == "Complete") {
                    DB::rollBack();
                    throw new \Exception("This transaction already completed!.");
                } else {
                    $data->status = "Complete";
                    $data->completions_at = Carbon::now();
                    $data->transfer_status = 2;
                    $data->feedback = $request->feedback;
                    $data->save();


                    $net_amount = $data->amount + $data->charge;

                    $api_endpoint = "";
                    $partner_api_key = Api::where('id', $data->api_id)->where('type', 'Admin')->lockForUpdate()->firstOrFail();
                    if ($partner_api_key) {
                        $partner_api_key->balance -= $net_amount;
                        $partner_api_key->save();
                        $api_endpoint = $partner_api_key->api_endpoint_withdrawal;

                        $Log = new Log();
                        $Log->date_time = $data->updated_at;
                        $Log->final_amount = - ($data->amount + $data->charge);
                        $Log->balance = $partner_api_key->balance;
                        $Log->transection_type = 2;
                        $Log->transection_id = $data->id;
                        $Log->partner_id = $data->api_id;
                        $Log->source = 'AdminPanel';
                        $Log->save();

                        $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $partner_api_key->id)->whereDate('created_at', '>=', $data->created_at)->get();
                        foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                            $amount_to_update = $DailyPartnerSummary_record->closing_balance - ($data->amount + $data->charge);
                            $amount_to_update = round($amount_to_update, 2);
                            // $amount_to_update = floor($amount_to_update * 100) / 100;
                            $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                            $DailyPartnerSummary_record->save();

                            $summary_log = new DailyPartnerSummaryLog();
                            $summary_log->partner_id = $partner_api_key->id;
                            $summary_log->partner_balance = $partner_api_key->balance;
                            $summary_log->payment_id = $data->id;
                            $summary_log->total_amount = - ($data->amount + $data->charge);
                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                            $summary_log->source = 'AdminPanel';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $data->id)->where('type', 2)->where('status', 0)->get();
                        foreach ($PartnerCommissions as $PartnerCommission) {
                            $PartnerCommission->status = 1;
                            $PartnerCommission->save();
                            $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->firstOrFail();
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


                        $account = EWalletAccount::where('e_wallet_name', $data->e_wallet_name)
                            ->where('account_no', $data->e_wallet_phone_number)
                            ->lockForUpdate()->first();

                        if (!$account) {
                            DB::rollBack();
                            throw new \Exception("No E-wallet account Available at this time to proceed this request.");
                        }

                        if ($account) {
                            //E-Wallet Account Log Save
                            $previous_account_balance = number_format($account->balance, 2, '.', '');

                            $account->balance -= $data->amount;
                            $account->daily_sent += $data->amount;
                            $account->monthly_sent += $data->amount;
                            $account->send += $data->amount;
                            $account->save();

                            $e_wallet_log_save = new EWalletLog();
                            $e_wallet_log_save->previous_balance = $previous_account_balance;
                            $e_wallet_log_save->amount = -$data->amount;
                            $e_wallet_log_save->charge = isset($data->fee) ? $data->fee : 0.00;
                            $e_wallet_log_save->commission = isset($data->commission) ? $data->commission : 0.00;
                            $e_wallet_log_save->final_amount = (-$data->amount - $data->fee + $data->commission);
                            $e_wallet_log_save->balance = ($previous_account_balance + $e_wallet_log_save->final_amount);
                            $e_wallet_log_save->transaction_type = 2;
                            $e_wallet_log_save->transaction_id = $data->id;
                            $e_wallet_log_save->account_id = $account->id;
                            $e_wallet_log_save->source = "action";
                            $e_wallet_log_save->save();


                            $e_wallet_charge = 0;
                            $count_payouts = Payout::where('e_wallet_name', $data->e_wallet_name)->where('e_wallet_phone_number', $data->e_wallet_phone_number)->where('status', 'Complete')->whereDate('date_time', $data->date)->count();
                            if ($count_payouts >= $account->free_transections_day) {
                                $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->where('from_amount', '<=', $data->amount)->where('to_amount', '>=', $data->amount)->first();
                                if ($e_wallet_charges) {
                                    $e_wallet_charge = $e_wallet_charges->wcharges;
                                    if ($e_wallet_charges->wcharges_type == 2) {
                                        $e_wallet_charge = $e_wallet_charges->wcharges * $data->amount / 100;
                                    }
                                } else {
                                    $e_wallet_charges = EWalletCharge::where('account_id', $account->id)->orderBy('to_amount', 'desc')->first();
                                    if ($e_wallet_charges) {
                                        $e_wallet_charge = $e_wallet_charges->wcharges;
                                        if ($e_wallet_charges->wcharges_type == 2) {
                                            $e_wallet_charge = $e_wallet_charges->wcharges * $data->amount / 100;
                                        }
                                    }
                                }
                            }

                            $data->e_wallet_charges = $e_wallet_charge;
                            $data->save();
                        }

                        $commit = 1;
                        DB::commit();

                        if (!empty($api_endpoint) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($data->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $data->e_wallet_name,
                                "id" => strval($data->id),
                                'transaction_type' => 'Withdrawal',
                                "user_account_no" => strval($data->user_account_no),
                            ));
                            $secretKey = $partner_api_key->secret_key;
                            $hash = hash("sha256", $string_to_hash);
                            $hmac = hash_hmac('sha256', $hash, $secretKey);
                            $timestamp = time();
                            $combined = $hmac . $timestamp;
                            $sign = base64_encode($combined);

                            $datetime = Carbon::parse($data->date_time);

                            $api_date = $datetime->toDateString();   // '2025-05-19'
                            $api_time = $datetime->toTimeString();   // '15:43:00'

                            $array_data = [
                                'id' => $data->id,
                                'partner_transection_id' => $data->partner_transection_id,
                                'transaction_type' => 'Withdrawal',
                                'e_wallet_name' => $data->e_wallet_name,
                                'amount' => $this->convertStringToNumber($data->amount),
                                'user_account_no' => $data->user_account_no,
                                'txn_id' => $data->txn_id,
                                'e_wallet_phone_number' => $data->e_wallet_phone_number,
                                'e_wallet_type' => $data->e_wallet_type,
                                'charges' => $this->convertStringToNumber($data->charge),
                                'status' => $data->status,
                                'completion_date' => $api_date,
                                'completion_time' => $api_time,
                                'created_at' => $data->created_at,
                                'updated_at' => $data->updated_at,
                                'sign' => $sign,
                                'remarks' => $request->feedback,
                            ];

                            if (!empty($data->member_id)) {
                                $array_data['member_id'] = $data->member_id;
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
            if ($commit == 0) {
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
                if ($pre_payout->transfer_status == 3) {
                    $pre_payout->transfer_status = 1;
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
                    $e_wallet_log_save->final_amount = ($pre_payout->amount + $pre_payout->fee - $pre_payout->commission);
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
                    $e_wallet_log_save->final_amount = (-$pre_payout->amount - $pre_payout->fee + $pre_payout->commission);
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

            $datetime = Carbon::parse($payout->date_time);

            $api_date = $datetime->toDateString();   // '2025-05-19'
            $api_time = $datetime->toTimeString();   // '15:43:00'

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
                'completion_date' => $api_date,
                'completion_time' => $api_time,
                'created_at' => $payout->created_at,
                'updated_at' => $payout->updated_at,
                'sign' => $sign,
                // 'remarks' => $payout_log->feedback,

            ];

            if (!empty($payout->member_id)) {
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
        $query = Api::where('type', 'Admin')->where('acc_type','Partner')->select([
            'id', 'name', 'username', 'acc_type', 'website', 'api_endpoint_deposit',
            'api_endpoint_withdrawal', 'redirect_url', 'api_key', 'secret_key', 'balance',
            'min_deposit', 'min_withdrawal', 'status', 'password_string', 'sign', 'txn_verification'
        ]);

        // Default to show_all = 1
        $showAll = $request->get('show_all', '1');

        $records = $showAll == '1'
            ? $query->get()
            : $query->paginate(20);

        $pageTitle = "Manage APIs";

        return view('admin.payout.api', compact('records', 'pageTitle', 'showAll'));
    }




    public function agentlist(Request $request)
    {
        $query = Api::where('type', 'Admin')->where('acc_type','Agent')->select([
            'id', 'name', 'username', 'balance','status'
        ]);

        // Default to show_all = 1
        $showAll = $request->get('show_all', '1');

        $records = $showAll == '1'
            ? $query->get()
            : $query->paginate(20);

        $pageTitle = "Agent List";
        // dd($records);

        return view('admin.payout.agent', compact('records', 'pageTitle', 'showAll'));
    }

    // Partner controller

    public function apisReset($id)
    {
        $api = Api::findOrFail($id);
        $website = $api->website;
        $TwoStepVerification = TwoStepVerification::where('user_id', $id)
            ->first();
        if ($TwoStepVerification) {
            $TwoStepVerification->g_auth_status = 'No';
            $TwoStepVerification->save();
        }


        return redirect()->route('admin.apis')->with('success', 'API Reset successfully.');
    }


    public function apisDelete($id)
    {
        try {
            $api = Api::findOrFail($id);
            // $api_key = $api->api_key;
            // Api::where('api_key', $api_key)->delete();

            $api->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'API deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete API. ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }


    public function updateApi(Request $request, $id)
    {

        $api = Api::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'username' => 'required|string|max:100',
                'email' => 'nullable|email|max:100',
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
                'website' => 'nullable|string|max:255',
                'api_endpoint_deposit' => 'nullable|string|max:200',
                'api_endpoint_withdrawal' => 'nullable|string|max:200',
                'admin_access' => 'nullable|string',
                // 'type' => 'required|string|max:50',
                'api_key' => 'required|string|max:255',
                // 'last_login' => 'nullable|string|max:50',
                // 'remember_token' => 'nullable|string|max:100',
                // 'balance' => 'nullable|numeric',
                'min_deposit' => 'nullable|numeric',
                'min_withdrawal' => 'required|numeric',
                'acc_type' => 'required|string|max:20',
                // 'parent_id' => 'nullable|integer',
                'sign' => 'required|boolean',
                'secret_key' => 'nullable|string|max:255',
                'txn_verification' => 'required|boolean',
                'redirect_url' => 'nullable|string|max:500',
                'timezone' => 'nullable|string|max:255',
                'status' => 'required|boolean',
                'category_id' => 'nullable',
            ]);


        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
            $validated['password_string'] = $request->password;
        } else {
            unset($validated['password']);
        }

        $api->update($validated);

        return redirect()->back()->with('success', 'API record updated successfully.');
    }

    public function agentupdateApi(Request $request, $id)
    {

        $api = Api::findOrFail($id);

            $validated = $request->validate([
                // 'name' => 'required|string|max:100',
                'username' => 'required|string|max:100',
                // 'email' => 'nullable|email|max:100',
                // 'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
                // 'website' => 'nullable|string|max:255',
                // 'api_endpoint_deposit' => 'nullable|string|max:200',
                // 'api_endpoint_withdrawal' => 'nullable|string|max:200',
                // 'admin_access' => 'nullable|string',
                // // 'type' => 'required|string|max:50',
                // 'api_key' => 'required|string|max:255',
                // // 'last_login' => 'nullable|string|max:50',
                // // 'remember_token' => 'nullable|string|max:100',
                // // 'balance' => 'nullable|numeric',
                // 'min_deposit' => 'nullable|numeric',
                // 'min_withdrawal' => 'required|numeric',
                // 'acc_type' => 'required|string|max:20',
                // // 'parent_id' => 'nullable|integer',
                // 'sign' => 'required|boolean',
                // 'secret_key' => 'nullable|string|max:255',
                // 'txn_verification' => 'required|boolean',
                // 'redirect_url' => 'nullable|string|max:500',
                // 'timezone' => 'nullable|string|max:255',
                // 'status' => 'required|boolean',
                // 'category_id' => 'nullable',
            ]);


        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }
        $validated['password_string'] = $request->password;
        $api->update($validated);

        return redirect()->back()->with('success', 'API record updated successfully.');
    }



    public function apisAddByParent(Request $request)
    {
        //pendingtocheck as this fn not working
        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string|min:5',
        ]);

        // Permissions array
        $permissionsArray = [
            "partner.dashboard",
            "partner.staff",
            "partner.storeStaff",
            "partner.updateStaff",
            "partner.apis.delete",
            "partner.payment.report",
            "partner.payment.report.search",
            "partner.payment.report.daily",
            "partner.payment.report.daily.search",
            "partner.payment.report.all",
            "partner.payment.report.all.search",
            "partner.payout-log",
            "partner.payout-request",
            "partner.payout-log.search",
            "partner.payout-action",
            "partner.payout-report",
            "partner.payout-report.search",
            "partner.payout.report.daily",
            "partner.payout.report.daily.search"
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
            // Return validation errors as JSON
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Permissions list
        $permissionsArray = [
            "partner.dashboard",
            "partner.staff",
            "partner.storeStaff",
            "partner.updateStaff",
            "partner.apis.delete",
            "partner.payment.report",
            "partner.payment.report.search",
            "partner.payment.report.daily",
            "partner.payment.report.daily.search",
            "partner.payment.report.all",
            "partner.payment.report.all.search",
            "partner.payout-log",
            "partner.payout-request",
            "partner.payout-log.search",
            "partner.payout-action",
            "partner.payout-report",
            "partner.payout-report.search",
            "partner.payout.report.daily",
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

        return response()->json([
            'status' => 'success',
            'message' => 'API Added Successfully',
        ]);
    }


    public function agentAdd(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string',
            'status' => 'required',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            // Return validation errors as JSON
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
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

        // Create and save API entry
        Api::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => '',
            'phone' => '',
            'password' => bcrypt($request->password), // Secure password hashing
            'status' => $request->status,
            'website' => '',
            'api_endpoint_deposit' => '',
            'api_endpoint_withdrawal' => '',
            'redirect_url' => '',
            'acc_type' => '',
            'admin_access' => $permissionsArray,
            'type' => 'Admin',
            'balance' => 0,
            'acc_type' => 'Agent',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'API Added Successfully',
        ]);
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
        DB::beginTransaction(); // Start a transaction
        try {
            if ($request->amount_type == 2) {
                $amount = -$request->amount;
            } else {
                $amount = $request->amount;
            }

            $charges = $request->charges;
            if ($request->charges_type == 2) {
                $charges = ($request->amount / 100) * $request->charges;
            }

            $api = Api::where('id', $request->partner_id)->lockForUpdate()->firstOrFail();
            $api->balance += ($amount - $charges);
            $api->save();

            $new_api_transaction = new ApiTransaction;
            $new_api_transaction->amount = $amount;
            $new_api_transaction->adjustment = $request->adjustment;
            $new_api_transaction->source = $request->source;
            $new_api_transaction->txn = $request->txn;
            $new_api_transaction->reason = $request->reason;
            $new_api_transaction->partner_id = $request->partner_id;
            $new_api_transaction->charges = $charges;
            $new_api_transaction->save();

            $Log = new Log();
            $Log->date_time = $new_api_transaction->created_at;
            $Log->final_amount = $amount - $charges;
            $Log->balance = $api->balance;
            $Log->transection_type = 3;
            $Log->transection_id = $new_api_transaction->id;
            $Log->partner_id = $new_api_transaction->partner_id;
            $Log->source = 'APIBalanceAdd';
            $Log->save();

            DB::commit();
            session()->flash('success', 'Successfully Updated Balance');
            return back();
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
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
        $commissions = Commission::where('category_id', $id)->get();
        $cron_commissions = CronCommission::where('category_id', $id)->get();

        $categories = Category::with(['gateways' => function($query) {
            $query->where('status', 1);
        }])->where('status', 1)->get();


        $allgateways = Gateway::where('status', 1)->get();

        $gateways = [];
        foreach ($categories as $category) {
            foreach ($category->gateways as $gateway) {
                $gateways[$category->name][] = $gateway->name;
            }
        }

        $pageTitle = "Manage Commissions";

        $records = "";

        return view('admin.payout.commission', compact(
            'records', 'pageTitle', 'commissions', 'cron_commissions','id' ,'gateways','categories','allgateways'
        ));
    }

    public function partnerCommission($id)
    {
        $api = Api::where('id', $id)->first();
        $partners = Api::where('id', '!=', $id)->where('acc_type','Agent')->get();
        // $cron_commissions = CronCommission::where('category_id', $id)->get();
        $user_id = $api->id;
        $commissions = Commission::where('category_id', $api->category_id)->get();
        $gateways = Gateway::where('status', 1)->get();
        $pageTitle = "Partner Commissions";

        $records = "";

        return view('admin.payout.partner_commission', compact(
            'records',
            'pageTitle',
            'commissions',
            'id',
            'gateways',
            'partners',
            'user_id'
        ));
    }

    public function addpartnerCommission(Request $request)
    {

        $api = Api::findOrFail($request->user_id);
        $commissions = Commission::where('category_id', $api->category_id)->get();
        foreach ($commissions as $key => $commission) {
            $ParentCommission = ParentCommission::where('commission_id', $commission->id)->where('parent_id', $request->partner_id)->where('user_id', $request->user_id)->first();
            if ($ParentCommission) {
                $ParentCommission->update([
                    'from_amount' => $commission->from_amount,
                    'to_amount' => $commission->to_amount,
                    'deposit_percentage' => $request->deposit_percentage[$key],
                    'withdrawal_percentage' => $request->withdrawal_percentage[$key],
                    'parent_id' => $request->partner_id,
                    'user_id' => $request->user_id,
                    'type' => $commission->type,
                    'gateway_id' => $commission->gateway_id,
                    'commission_id' => $commission->id,
                ]);
            } else {
                ParentCommission::create([
                    'from_amount' => $commission->from_amount,
                    'to_amount' => $commission->to_amount,
                    'deposit_percentage' => $request->deposit_percentage[$key],
                    'withdrawal_percentage' => $request->withdrawal_percentage[$key],
                    'parent_id' => $request->partner_id,
                    'user_id' => $request->user_id,
                    'type' => $commission->type,
                    'gateway_id' => $commission->gateway_id,
                    'commission_id' => $commission->id,
                ]);
            }


        }
        return redirect()->route('admin.merchant.profile', ['id' => $request->user_id]);
    }


    public function partnerCommissionedit($cid)
    {
        $ParentCommission = ParentCommission::where('id', $cid)->first();
        $id = $ParentCommission->user_id;
        $parent_id = $ParentCommission->parent_id;
        $user_id = $ParentCommission->user_id;


        $partner = Api::where('id', $parent_id)->first();


        $commission = Commission::where('id', $ParentCommission->commission_id)->first();


        $gateways = Gateway::where('status', 1)->get();
        $pageTitle = "Partner Commissions";

        $records = "";

        return view('admin.payout.partner_commission_edit', compact(
            'records',
            'pageTitle',
            'commission',
            'id',
            'gateways',
            'partner',
            'user_id',
            'ParentCommission',
            'parent_id',
            'cid'
        ));
    }

    public function editpartnerCommission(Request $request)
    {

        $cid = $request->cid;
        $ParentCommission = ParentCommission::where('id', $cid)->first();
        $ParentCommission->update([
            'deposit_percentage' => $request->deposit_percentage,
            'withdrawal_percentage' => $request->withdrawal_percentage,
        ]);


        return redirect()->route('admin.merchant.profile', ['id' => $request->user_id]);
    }


    public function commissionDelete($id)
    {
        $ParentCommission = ParentCommission::where('id', $id)->first();
        $user_id = $ParentCommission->user_id;
        if ($ParentCommission) {
            $ParentCommission->delete();
        }
        return redirect()->route('admin.merchant.profile', ['id' => $user_id]);
    }

    public function toggleStatusApi(Request $request)
    {
        $api = Api::find($request->id);

        if (!$api) {
            return response()->json(['status' => 'error', 'message' => 'API not found.']);
        }

        $type = $request->type; // expected: 'status', 'sign', or 'txn_verification'

        if (!in_array($type, ['status', 'sign', 'txn_verification'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid field type.']);
        }

        $api->$type = $request->value;
        $api->save();

        return response()->json(['status' => 'success', 'message' => ucfirst($type) . ' updated.']);
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
        $account = EWalletAccount::where('id', $id)->first();

       if($account){
        $account->delete();
        return redirect()->back()->with('success', 'Account deleted successfully');
       }else{
        return redirect()->back()->with('error', 'Account not found');
       }
    }

    public function editAccount($id)
    {
        $pageTitle = __('accounts.edit_new_account');
        $categories = Category::select('name', 'id')->get();
        $methods = Gateway::select('name', 'id')->where('status', 1)->get();
        $groups = Group::all();
        $users_locations=UserLocation::where('status' , 1)->get();

        $e_wallet_account= EWalletAccount::with('timeSlots')->find($id);
        if (!$e_wallet_account) {
            return redirect()->route('admin.ewallet.accounts.details')->with('error', 'Account not found.');
        }
        $savedSlots = $e_wallet_account->timeSlots->pluck('time_saved')->toArray();
        $selectedGroupIds = AccountGroup::where('account_id' , $e_wallet_account->id)->pluck('group_id')->toArray();


        return view('admin.payout.edit_account', compact('pageTitle', 'categories', 'methods' , 'groups' ,'users_locations' , 'e_wallet_account' , 'savedSlots' ,'selectedGroupIds'));
    }
    public function updateAccount(Request $request , $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:gateways,id',

            // Configuration validation
            'daily_limit' => 'required|integer|min:0',
            'daily_limit_withdrawal' => 'required|integer|min:0',
            'monthly_limit' => 'required|integer|min:0',
            'monthly_limit_withdrawal' => 'required|integer|min:0',
            'daily_limit_transaction' => 'required|integer|min:0',
            'daily_limit_withdrawal_transaction' => 'required|integer|min:0',
            'monthly_limit_transaction' => 'required|integer|min:0',
            'monthly_limit_withdrawal_transaction' => 'required|integer|min:0',
            'max_transaction_per_minute' => 'required|integer|min:0',
            'max_amount_per_minute' => 'required|integer|min:0',

            // Threshold alerts
            'deposit_daily_limit_percentage' => 'required|integer|min:1|max:100',
            'withdrawal_daily_limit_percentage' => 'required|integer|min:1|max:100',
            'deposit_monthly_limit_percentage' => 'required|integer|min:1|max:100',
            'withdrawal_monthly_limit_percentage' => 'required|integer|min:1|max:100',
            'low_balance_amount' => 'required|integer|min:0',

            // Time slots
            'time_slots' => 'nullable|array',
            'time_slots.*' => 'string',

            // E-wallet accounts validation
            'e_wallet_name' => 'required|array',
            'e_wallet_name.*' => 'required|string',
            'device_name' => 'required|array',
            'device_name.*' => 'required|string',
            'account_number' => 'required|array',
            'account_number.*' => 'required|string',
            'account_group' => 'nullable|array',
            'account_group.*' => 'nullable|array',
            'account_group.*.*' => 'exists:groups,id',
            'account_type' => 'required|array',
            'account_type.*' => 'required|in:Agent,Merchant,Personal',
            'in_out' => 'required|array',
            'in_out.*' => 'required|in:Deposit,Withdrawal,Both',
            'location' => 'nullable|array',
            'location.*' => 'nullable|exists:user_locations,id',
            'image' => 'nullable|array',
            'image.*' => 'nullable|image|mimes:jpeg,png|max:2048',

            'status' => 'nullable|boolean',
        ]);
        // dd($request->all());
        try {
            DB::beginTransaction();

            // Process time slots
            $timeSlots = $request->time_slots ?? [];
            $applyTimeLimit = !empty($timeSlots) ? 0 : 0;
            $firstAccountId = $request->first_account_id;
            foreach ($request->e_wallet_name as $index => $name) {
                // $imagePath = null;
                // Check if image is uploaded for this index
                if ($request->hasFile('image.' . $index)) {
                    $file = $request->file('image.' . $index);
                    $destinationPath = base_path('assets/uploads/withdraw');

                    // Make sure the folder exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    // Check if this is the account being updated (i.e., first_account_id)
                    $existingImage = null;
                    if ($index == 0 && $request->first_account_id) {
                        $existingAccount = EWalletAccount::find($request->first_account_id);
                        if ($existingAccount && $existingAccount->image) {
                            $existingImagePath = $destinationPath . '/' . $existingAccount->image;
                            if (file_exists($existingImagePath)) {
                                unlink($existingImagePath); // Delete old image
                            }
                        }
                    }

                    // Generate a new unique filename and move the file
                    $imagePath = time() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $imagePath);
                }

                // Prepare account data
                $accountData = [
                    'category_id' => $request->category_id,
                    'gateway_id'=> $request->account_id,
                    'e_wallet_name' => $name,
                    'account_no' => $request->account_number[$index],
                    'type' => $request->account_type[$index],
                    'account_type' => $request->in_out[$index],
                    'daily_limit' => $request->daily_limit,
                    'monthly_limit' => $request->monthly_limit,
                    'daily_limit_transaction' => $request->daily_limit_transaction,
                    'monthly_limit_transaction' => $request->monthly_limit_transaction,
                    'daily_limit_withdrawal' => $request->daily_limit_withdrawal,
                    'monthly_limit_withdrawal' => $request->monthly_limit_withdrawal,
                    'daily_limit_withdrawal_transaction' => $request->daily_limit_withdrawal_transaction,
                    'monthly_limit_withdrawal_transaction' => $request->monthly_limit_withdrawal_transaction,
                    'deposit_daily_limit_percentage' => $request->deposit_daily_limit_percentage,
                    'withdrawal_daily_limit_percentage' => $request->withdrawal_daily_limit_percentage,
                    'deposit_monthly_limit_percentage' => $request->deposit_monthly_limit_percentage,
                    'withdrawal_monthly_limit_percentage' => $request->withdrawal_monthly_limit_percentage,
                    'max_transaction_per_minute' => $request->max_transaction_per_minute,
                    'max_amount_per_minute' => $request->max_amount_per_minute,
                    'low_balance_amount' => $request->low_balance_amount,
                    'apply_time_limit' => $applyTimeLimit,

                    'status' => $request->status ?? 0,
                    'device_name' => $request->device_name[$index],
                    'location_id' => $request->location[$index] ?? null,
                ];

                if(isset($imagePath)   && !empty($imagePath)) {
                    $accountData['image'] = $imagePath; // Add image path if it exists
                }

                // First iteration: update existing account
                if ($index === 0 && $firstAccountId) {
                    $account = EWalletAccount::findOrFail($firstAccountId);
                    $account->update($accountData);

                    // Delete existing time slots and groups before re-adding
                    EWalletAccountTimeSlot::where('e_wallet_account_id', $account->id)->delete();
                    AccountGroup::where('account_id', $account->id)->delete();
                } else {
                    // Create new account for remaining entries
                    $account = EWalletAccount::create(array_merge($accountData, [
                        'balance' => 0,
                        'live_balance' => 0,
                        'daily_received' => 0,
                        'monthly_received' => 0,
                        'daily_sent' => 0,
                        'monthly_sent' => 0,
                        'send' => 0,
                        'received' => 0,
                    ]));
                }

                // Handle time slots
                if (!empty($timeSlots)) {
                    foreach ($timeSlots as $slot) {
                        [$fromTimeStr, $toTimeStr] = explode(' - ', $slot);
                        $fromTime = date('H:i:s', strtotime($fromTimeStr));
                        $toTime = date('H:i:s', strtotime($toTimeStr));

                        EWalletAccountTimeSlot::create([
                            'e_wallet_account_id' => $account->id,
                            'time_saved' => $slot,
                            'from_time' => $fromTime,
                            'to_time' => $toTime,
                        ]);
                    }
                }

                // Handle account groups
                if (!empty($request->account_group[$index])) {
                    foreach ($request->account_group[$index] as $groupId) {
                        AccountGroup::create([
                            'account_id' => $account->id,
                            'group_id' => $groupId,
                        ]);
                    }
                }
            }
            DB::commit();

            return redirect()->route('admin.ewallet.accounts.details')->with('success', 'E-Wallet accounts created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating accounts: ' . $e->getMessage());
        }
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
        // Validate input
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:e_wallet_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:plus,minus',
        ]);

        if ($validator->fails()) {
            // Return validation errors as JSON for AJAX
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $account = EWalletAccount::where('id', $request->account_id)->lockForUpdate()->firstOrFail();
            $previous_balance = number_format($account->balance, 2, '.', '');
            $amount = $request->amount;
            $isAddition = $request->type == "plus";

            $finalAmount = $isAddition ? $amount : -$amount;

            // Update balance
            $account->balance += $finalAmount;
            $account->save();

            // Create EWalletLog
            $e_wallet_log = EWalletLog::create([
                'account_id' => $account->id,
                'previous_balance' => $previous_balance,
                'charge' => 0.00,
                'commission' => 0.00,
                'amount' => $finalAmount,
                'final_amount' => $finalAmount,
                'balance' => $account->balance,
                'transaction_type' => $isAddition ? 5 : 6,
                'source' => 'accountBalanceAdd',
            ]);

            // Create AccountLog
            $transaction = AccountLog::create([
                'amount' => $amount,
                'type' => $request->type,
                'e_wallet_account_id' => $request->account_id,
            ]);

            $e_wallet_log->update(['transaction_id' => $transaction->id]);

            DB::commit();

            // Return success for AJAX
            return response()->json(['success' => true,'message' => 'Successfully Updated Balance'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['errors' => ['server' => ['Failed to update balance: ' . $e->getMessage()]]], 500);
        }
    }


   public function accountBalanceEdit(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:e_wallet_accounts,id',
            'amount' => 'required|numeric',
            'live_balance' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $account = EWalletAccount::where('id', $request->account_id)->lockForUpdate()->firstOrFail();
            $difference = $request->amount - $account->balance;
            $differenceLive = $request->live_balance - $account->live_balance;

            if ($difference == 0 && $differenceLive == 0) {
                return response()->json([
                    'message' => 'Same balance, no changes made.'
                ], 200);
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

            return response()->json([
                'success' => true,
                'message' => 'Balance updated successfully!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update balance.',
                'error' => $e->getMessage()
            ], 500);
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


        $count = count($request->from_amount);

        for ($i = 0; $i < $count; $i++) {
            $new_commission = Commission::where('id', $request->id[$i])->first();
            if ($new_commission) {
                $commission_id = $new_commission->id;
            } else {
                $new_commission = new Commission;
            }

            // Convert gateways to JSON (for storage) if selected
            $gateway_ids = isset($request->settlement_gateway[$i]) ? json_encode($request->settlement_gateway[$i]) : json_encode([]);
            $types = isset($request->type[$i]) ? json_encode($request->type[$i]) : json_encode([]);

            $new_commission->from_amount = $request->from_amount[$i];
            $new_commission->to_amount = $request->to_amount[$i];
            $new_commission->deposit_percentage = $request->deposit_percentage[$i];
            $new_commission->withdrawal_percentage = $request->withdrawal_percentage[$i];
            $new_commission->settlement_percentage = $request->settlement_percentage[$i];
            $new_commission->category_id = $request->category_id;
            $new_commission->category = $request->category[$i];

            $new_commission->type = $types;
            $new_commission->gateway_id = $gateway_ids; // store as JSON

            $new_commission->save();
        }

        session()->flash('success', 'Successfully Updated');
        return back();
    }


    public function apisCommissionAddold(Request $request)
    {


        $cron_commissions = CronCommission::where('category_id', $request->category_id)->get();
        foreach ($cron_commissions as $cron_commission) {
            $cron_commission->delete();
        }

        $new = 0;
        $commissions = Commission::where('category_id', $request->category_id)->get();
        foreach ($commissions as $commission) {
            $new = 1;
        }

        $count = count($request->from_amount);

        for ($i = 0; $i < $count; $i++) {
            $new_commission = Commission::where('id', $request->id[$i])->first();
            if ($new_commission) {
                $commission_id = $new_commission->id;
            } else {
                $commission_id = 0;
            }

            // Convert gateways to JSON (for storage) if selected
            $gateway_ids = isset($request->settlement_gateway[$i]) ? json_encode($request->settlement_gateway[$i]) : json_encode([]);
            $types = isset($request->type[$i]) ? json_encode($request->type[$i]) : json_encode([]);

            if ($new == 0) {
                if (!$new_commission) {
                    $new_commission = new Commission;
                }

                $new_commission->from_amount = $request->from_amount[$i];
                $new_commission->to_amount = $request->to_amount[$i];
                $new_commission->deposit_percentage = $request->deposit_percentage[$i];
                $new_commission->withdrawal_percentage = $request->withdrawal_percentage[$i];
                $new_commission->settlement_percentage = $request->settlement_percentage[$i];
                $new_commission->category_id = $request->category_id;
                $new_commission->category = $request->category[$i];

                $new_commission->type = $types;
                $new_commission->gateway_id = $gateway_ids; // store as JSON

                $new_commission->save();
            } else {
                $cron_commission = new CronCommission;

                $cron_commission->from_amount = $request->from_amount[$i];
                $cron_commission->to_amount = $request->to_amount[$i];
                $cron_commission->deposit_percentage = $request->deposit_percentage[$i];
                $cron_commission->withdrawal_percentage = $request->withdrawal_percentage[$i];
                $cron_commission->settlement_percentage = $request->settlement_percentage[$i];
                $cron_commission->category_id = $request->category_id;
                $cron_commission->commission_id = $commission_id;
                $cron_commission->category = $request->category[$i];

                $cron_commission->type = $types;
                $cron_commission->gateway_id = $gateway_ids;

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
        // dd($request->all());
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:gateways,id',

            // Configuration validation
            'daily_limit' => 'required|integer|min:0',
            'daily_limit_withdrawal' => 'required|integer|min:0',
            'monthly_limit' => 'required|integer|min:0',
            'monthly_limit_withdrawal' => 'required|integer|min:0',
            'daily_limit_transaction' => 'required|integer|min:0',
            'daily_limit_withdrawal_transaction' => 'required|integer|min:0',
            'monthly_limit_transaction' => 'required|integer|min:0',
            'monthly_limit_withdrawal_transaction' => 'required|integer|min:0',
            'max_transaction_per_minute' => 'required|integer|min:0',
            'max_amount_per_minute' => 'required|integer|min:0',

            // Threshold alerts
            'deposit_daily_limit_percentage' => 'required|integer|min:1|max:100',
            'withdrawal_daily_limit_percentage' => 'required|integer|min:1|max:100',
            'deposit_monthly_limit_percentage' => 'required|integer|min:1|max:100',
            'withdrawal_monthly_limit_percentage' => 'required|integer|min:1|max:100',
            'low_balance_amount' => 'required|integer|min:0',

            // Time slots
            'time_slots' => 'nullable|array',
            'time_slots.*' => 'string',

            // E-wallet accounts validation
            'e_wallet_name' => 'required|array',
            'e_wallet_name.*' => 'required|string',
            'device_name' => 'required|array',
            'device_name.*' => 'required|string',
            'account_number' => 'required|array',
            'account_number.*' => 'required|string',
            'account_group' => 'nullable|array',
            'account_group.*' => 'nullable|array',
            'account_group.*.*' => 'exists:groups,id',
            'account_type' => 'required|array',
            'account_type.*' => 'required|in:Agent,Merchant,Personal',
            'in_out' => 'required|array',
            'in_out.*' => 'required|in:Deposit,Withdrawal,Both',
            'location' => 'nullable|array',
            'location.*' => 'nullable|exists:user_locations,id',
            'image' => 'nullable|array',
            'image.*' => 'nullable|image|mimes:jpeg,png|max:2048',

            'status' => 'nullable|boolean',
        ]);
        // dd($request->all());
        try {
            DB::beginTransaction();

            // Process time slots
            $timeSlots = $request->time_slots ?? [];
            // $applyTimeLimit = !empty($timeSlots) ? 1 : 0;
            $applyTimeLimit = 0;

            // Process each e-wallet account
            foreach ($request->e_wallet_name as $index => $name) {
                $imagePath = null;

                if ($request->hasFile('image.' . $index)) {
                    $file = $request->file('image.' . $index);

                    // Define the root-level path
                    $destinationPath = base_path('assets/uploads/withdraw');

                    // Make sure the folder exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    // Generate a unique filename
                    $imagePath = time() . '_' . $file->getClientOriginalName();

                    $file->move($destinationPath, $imagePath);
                }

                // Create e-wallet account
                $account = EWalletAccount::create([
                    'category_id' => $request->category_id,
                    'gateway_id'=> $request->account_id,
                    'e_wallet_name' => $name,
                    'account_no' => $request->account_number[$index],
                    'type' => $request->account_type[$index],
                    'account_type' => $request->in_out[$index],
                    'daily_limit' => $request->daily_limit,
                    'monthly_limit' => $request->monthly_limit,
                    'daily_limit_transaction' => $request->daily_limit_transaction,
                    'monthly_limit_transaction' => $request->monthly_limit_transaction,
                    'daily_limit_withdrawal' => $request->daily_limit_withdrawal,
                    'monthly_limit_withdrawal' => $request->monthly_limit_withdrawal,
                    'daily_limit_withdrawal_transaction' => $request->daily_limit_withdrawal_transaction,
                    'monthly_limit_withdrawal_transaction' => $request->monthly_limit_withdrawal_transaction,
                    'deposit_daily_limit_percentage' => $request->deposit_daily_limit_percentage,
                    'withdrawal_daily_limit_percentage' => $request->withdrawal_daily_limit_percentage,
                    'deposit_monthly_limit_percentage' => $request->deposit_monthly_limit_percentage,
                    'withdrawal_monthly_limit_percentage' => $request->withdrawal_monthly_limit_percentage,
                    'max_transaction_per_minute' => $request->max_transaction_per_minute,
                    'max_amount_per_minute' => $request->max_amount_per_minute,
                    'low_balance_amount' => $request->low_balance_amount,
                    'apply_time_limit' => $applyTimeLimit,
                    // 'time_slots' => !empty($timeSlots) ? json_encode($timeSlots) : null,
                    'image' => $imagePath,
                    'status' => $request->status ?? 0,
                    'balance' => 0,
                    'live_balance' => 0,
                    'daily_received' => 0,
                    'monthly_received' => 0,
                    'daily_sent' => 0,
                    'monthly_sent' => 0,
                    'send' => 0,
                    'received' => 0,
                    'device_name' => $request->device_name[$index],
                    'location_id' => $request->location[$index],
                    // 'account_group' => $request->account_group[$index],
                ]);

                // Attach account groups
                // $account = EWalletAccount::latest()->first();
                if (!empty($timeSlots)) {
                    foreach ($timeSlots as $slot) {
                        // Split the time slot into from and to times
                        [$fromTimeStr, $toTimeStr] = explode(' - ', $slot);

                        // Convert to proper TIME format (HH:MM:SS)
                        $fromTime = date('H:i:s', strtotime($fromTimeStr));
                        $toTime = date('H:i:s', strtotime($toTimeStr));

                        $slotModel = new EWalletAccountTimeSlot();
                        $slotModel->e_wallet_account_id = $account->id;
                        $slotModel->time_saved = $slot;
                        $slotModel->from_time = $fromTime;
                        $slotModel->to_time = $toTime;
                        $slotModel->saveOrFail();
                    }
                }

                foreach($request->account_group[$index] as $groupId) {
                    $new_group=new AccountGroup();
                    $new_group->account_id = $account->id;
                    $new_group->group_id = $groupId;
                    $new_group->save();
                }

                // $account->groups()->attach($request->account_group[$index]);
            }

            $Setting = Setting::where('name', 'last_account_active')->first();
            $Setting->value = Carbon::now();
            $Setting->save();

            DB::commit();

            return redirect()->route('admin.account_management.add_account')->with('success', 'E-Wallet accounts created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating accounts: ' . $e->getMessage());
        }
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
        $records = Settlement::with('api')->latest('id')->paginate(50);

        $gateways = Settlement::select('source_name', DB::raw('COUNT(*) as count'))
            ->groupBy('source_name')
            ->get();

        $pageTitle = "Partners Settlements History";
        $partners = Api::where('type', 'Admin')->get();
        return view('admin.payout.settlement', compact('records', 'pageTitle', 'gateways', 'partners'));
    }

    public function storeSettlement(Request $request)
    {
        // Validate the request
        $request->validate([
            'partner' => 'required|exists:apis,id',
            'source' => 'required|in:Bank,EWallet',
            'source_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $sum = Settlement::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('partner_id', $request->partner)
                ->where('status', '1')
                ->sum('amount');

            $api_key = Api::findOrFail($request->partner);

            // Calculate charge
            $charge = 0;
            $commissions = Commission::where('category_id', $api_key->category_id)
                ->where('from_amount', '<=', $sum)
                ->where('to_amount', '>=', $sum)
                ->first();

            if (!$commissions) {
                $commissions = Commission::where('category_id', $api_key->category_id)
                    ->orderByDesc('to_amount')
                    ->first();
            }

            if ($commissions) {
                $charge = $commissions->settlement_percentage * $request->amount / 100;
            }

            if ($api_key->balance < $request->amount + $charge) {
                return response()->json([
                    'errors' => [
                        'amount' => ['You can only enter an amount less than your transferable settlement balance.']
                    ]
                ], 422);
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

            return response()->json([
                'message' => 'Settlement saved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function settlementSearch(Request $request)
    {
        $partners = Api::where('type', 'Admin')->get();
        // Start with the query builder
        $query = Settlement::with('api');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $query->whereDate('created_at', '>=', $request->from_date)
                ->whereDate('created_at', '<=', $request->to_date);
        } elseif (!empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        } elseif (!empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if (!empty($request->gateway)) {
            $query->where('source_name', $request->gateway);
        }

        if (!empty($request->partner)) {
            $query->where('partner_id', $request->partner);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        // Only call paginate AFTER applying all filters
        $records = $query->orderBy('id', 'DESC')->paginate(10);
        // Only call paginate AFTER applying all filters


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
                // ->where('status', '!=', 1)
                ->lockForUpdate()
                ->first();

            if ($Settlement->status == 1) {
                DB::rollBack();
                throw new \Exception('Settlement already approved.');
            }

            $Settlement->status = 1;
            if (!$Settlement->save()) {
                DB::rollBack();
                throw new \Exception('Failed to save Settlement record.');
            }
            // dd('hello');ok


            $api = Api::where('id', $Settlement->partner_id)->lockForUpdate()->firstOrFail();
            $api->balance -= $Settlement->net_amount;
            // dd('hello');ok
            if (!$api->save()) {
                DB::rollBack();
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
                DB::rollBack();
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
        DB::beginTransaction();
        try {
            // $Settlement = Settlement::findOrFail($id);
            $Settlement = Settlement::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($Settlement->status == 2) {
                DB::rollBack();
                throw new \Exception('Already Rejected Settlement.');
            } else if ($Settlement->status == 1) {

                $Settlement->status = 2;
                $Settlement->save();

                $api = Api::where('id', $Settlement->partner_id)->lockForUpdate()->firstOrFail();
                $api->balance += $Settlement->net_amount;

                if (!$api->save()) {
                    DB::rollBack();
                    throw new \Exception('Failed to save API balance update.');
                }
                // dd('hello1');ok

                $Log = new Log();
                $Log->date_time = $Settlement->created_at;
                $Log->final_amount = $Settlement->net_amount;
                $Log->balance = $api->balance;
                $Log->transection_type = 8;
                $Log->transection_id = $Settlement->id;
                $Log->partner_id = $Settlement->partner_id;
                $Log->source = 'rejectSettlement';
                if (!$Log->save()) {
                    throw new \Exception('Failed to save Log entry.');
                }
                // dd('hello6');

                $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $api->id)->whereDate('created_at', '>=', $Settlement->created_at)->get();
                foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                    $amount_to_update = $DailyPartnerSummary_record->closing_balance + $Settlement->net_amount;
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
                    $summary_log->source = 'rejectSettlement';
                    $summary_log->save();
                }


                session()->flash('success', 'Successfully Rejected');
            } else {
                $Settlement->status = 2;
                $Settlement->save();

                session()->flash('success', 'Successfully Rejected');
            }

            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to Approve Settlement: ' . $e->getMessage());
            return back()->withInput();
        }
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

        $e_wallet_accounts = EWalletAccount::get();
        $e_wallet_transections = EWalletTransfer::whereDate('transaction_date_time', '=', $from_date)->orderBy('created_at', 'desc')->paginate(50);
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

        if ($request->category == "E-wallet to E-wallet") {
            $from_e_wallet_accounts = EWalletAccount::findOrFail($transfer_from);
            $to_e_wallet_accounts = EWalletAccount::findOrFail($transfer_to);
            $EWalletTransaction->from_e_wallet_id = $from_e_wallet_accounts->id;
            $EWalletTransaction->from_account_no = $from_e_wallet_accounts->account_no;
            $EWalletTransaction->to_e_wallet_id = $to_e_wallet_accounts->id;
            $EWalletTransaction->to_account_no = $to_e_wallet_accounts->account_no;
            $EWalletTransaction->e_wallet = $from_e_wallet_accounts->e_wallet_name;
        } elseif ($request->category == "Bank to E-wallet") {
            $to_e_wallet_accounts = EWalletAccount::findOrFail($transfer_to);
            $EWalletTransaction->from_e_wallet_id = 0;
            $EWalletTransaction->from_account_no = $request->transfer_from2;
            $EWalletTransaction->to_e_wallet_id = $to_e_wallet_accounts->id;
            $EWalletTransaction->to_account_no = $to_e_wallet_accounts->account_no;
            $EWalletTransaction->e_wallet = $to_e_wallet_accounts->e_wallet_name;
        } elseif ($request->category == "E-wallet to Bank") {
            $from_e_wallet_accounts = EWalletAccount::findOrFail($transfer_from);
            $EWalletTransaction->from_e_wallet_id = $from_e_wallet_accounts->id;
            $EWalletTransaction->from_account_no = $from_e_wallet_accounts->account_no;
            $EWalletTransaction->to_e_wallet_id = 0;
            $EWalletTransaction->to_account_no = $request->transfer_to2;
            $EWalletTransaction->e_wallet = $from_e_wallet_accounts->e_wallet_name;
        }

        if ($EWalletTransaction->from_e_wallet_id > 0) {
            $matched = 0;
            $SmsLog = SmsLog::where('e_wallet_name', $from_e_wallet_accounts->e_wallet_name)->where('txn', $request->txn_id)->where('e_wallet_no', $from_e_wallet_accounts->account_no)->orderBy('id', 'desc')->first();
            if ($SmsLog) {
                if ($SmsLog->matched == 1) {
                    $matched = 1;
                }
            }

            if ($matched == 0) {
                $from_e_wallet_accounts->balance = $from_e_wallet_accounts->balance - $request->amount - $request->charges + $request->comission;
                $from_e_wallet_accounts->live_balance = $from_e_wallet_accounts->live_balance - $request->amount - $request->charges + $request->comission;
                $from_e_wallet_accounts->save();
            }
        }

        if ($EWalletTransaction->to_e_wallet_id > 0) {
            $matched = 0;
            $SmsLog = SmsLog::where('e_wallet_name', $to_e_wallet_accounts->e_wallet_name)->where('txn', $request->txn_id)->where('e_wallet_no', $to_e_wallet_accounts->account_no)->orderBy('id', 'desc')->first();
            if ($SmsLog) {
                if ($SmsLog->matched == 1) {
                    $matched = 1;
                }
            }

            if ($matched == 0) {
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

            $payout = new Payout();


            if ($api_key->acc_type == "Partner") {

                $current_time = Carbon::now('Asia/Dhaka');

                $this->updateLimits();
                $this->updateEWallets();


                $Setting = Setting::where('name', 'last_account_active')->first();





                $now = Carbon::now();
                $startOfToday = $now->copy()->startOfDay();
                $startOfMonth = $now->copy()->startOfMonth();
                $oneMinuteAgo = $now->copy()->subMinute();

                // Query
                $results = Payout::selectRaw('
                        e_wallet_phone_number,
                        COUNT(CASE WHEN created_at >= ? THEN 1 END) AS counts_for_round_robin,
                        COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS today_count,
                        COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS month_count,
                        COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS one_min_count,
                        SUM(CASE WHEN completions_at >= ? AND status = "Complete" THEN amount ELSE 0 END) AS one_min_sum
                    ', [
                        $Setting->value,
                        $startOfToday,
                        $startOfMonth,
                        $oneMinuteAgo,
                        $oneMinuteAgo
                    ])
                    ->where('e_wallet_name', $request->e_wallet_name)
                    ->whereNotNull('e_wallet_phone_number')
                    ->where('e_wallet_phone_number', '!=', '')
                    ->groupBy('e_wallet_phone_number')
                    ->get();


                    // ->where('created_at', '>=', $startOfMonth)

                    $all_accounts = [];

                    foreach($results as $result){
                        $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
                        $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
                        $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
                        $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
                        $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
                    }




                    $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                        ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                        ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$request->amount])
                        ->where('status', 1)
                        ->where('max_withdrawal_amount', '>=', $request->amount)
                        ->whereIn('account_type', ['Withdrawal', 'Both'])
                        ->with('timeSlots')
                        ->get()
                        ->filter(function ($single_account) use ($all_accounts, $current_time) {
                            $phone = $single_account->account_no;

                            // Check all transaction limits
                            $validTransactionLimits = !isset($all_accounts[$phone]) || (
                                $single_account->daily_limit_transaction > ($all_accounts[$phone]['today_count'] ?? 0) &&
                                $single_account->monthly_limit_transaction > ($all_accounts[$phone]['month_count'] ?? 0) &&
                                $single_account->max_transaction_per_minute > ($all_accounts[$phone]['one_min_count'] ?? 0) &&
                                $single_account->max_amount_per_minute > ($all_accounts[$phone]['one_min_sum'] ?? 0)
                            );

                            // Check if at least one time slot matches

                            $validTimeSlot = $single_account->timeSlots->contains(function ($slot) use ($current_time) {
                                $from = Carbon::parse($slot->from_time);
                                $to = Carbon::parse($slot->to_time);

                                return $current_time->between($from, $to);
                            });


                            return $validTransactionLimits && $validTimeSlot;
                        })
                        ->sortBy(function ($single_account) use ($all_accounts) {
                            return $all_accounts[$single_account->account_no]['counts_for_round_robin'] ?? 0;
                        })
                        ->values()
                        ->first();





                // $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                //     ->where('type', 'Agent')
                //     ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                //     ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$request->amount])
                //     ->where('status', 1)
                //     ->where('max_withdrawal_amount', '>=', $request->amount)
                //     ->whereIn('account_type', ['Withdrawal', 'Both'])
                //     ->where(function ($query) use ($current_time) {
                //         $query->where('apply_time_limit', 0)
                //             ->orWhere(function ($query) use ($current_time) {
                //                 $query->where('apply_time_limit', 1)
                //                     ->where('from_time', '<=', $current_time)
                //                     ->where('to_time', '>=', $current_time);
                //             });
                //     })
                //     ->orderBy('daily_sent', 'asc')
                //     ->first();
                // if (!$account) {
                //     $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                //         ->where('type', 'Merchant')
                //         ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                //         ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$request->amount])
                //         ->where('status', 1)
                //         ->where('max_withdrawal_amount', '>=', $request->amount)
                //         ->whereIn('account_type', ['Withdrawal', 'Both'])
                //         ->where(function ($query) use ($current_time) {
                //             $query->where('apply_time_limit', 0)
                //                 ->orWhere(function ($query) use ($current_time) {
                //                     $query->where('apply_time_limit', 1)
                //                         ->where('from_time', '<=', $current_time)
                //                         ->where('to_time', '>=', $current_time);
                //                 });
                //         })
                //         ->orderBy('daily_sent', 'asc')
                //         ->first();
                //     if (!$account) {
                //         $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                //             ->where('type', 'Personal')
                //             ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                //             ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$request->amount])
                //             ->where('status', 1)
                //             ->where('max_withdrawal_amount', '>=', $request->amount)
                //             ->whereIn('account_type', ['Withdrawal', 'Both'])
                //             ->where(function ($query) use ($current_time) {
                //                 $query->where('apply_time_limit', 0)
                //                     ->orWhere(function ($query) use ($current_time) {
                //                         $query->where('apply_time_limit', 1)
                //                             ->where('from_time', '<=', $current_time)
                //                             ->where('to_time', '>=', $current_time);
                //                     });
                //             })
                //             ->orderBy('daily_sent', 'asc')
                //             ->first();
                //     }
                // }



                if (!$account) {
                    return response()->json(['message' => 'No E-wallet account Available at this time to proceed this request.'], 404);
                }

                $payout->transfer_status = 2;
                $payout->e_wallet_phone_number = $account->account_no;
                $payout->e_wallet_type = $account->type;
            }




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

                $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($commissions) {
                    $charge = $commissions->withdrawal_percentage * $request->amount / 100;
                } else {
                    $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($commissions) {
                        $charge = $commissions->withdrawal_percentage * $request->amount / 100;
                    }
                }
            }




                $previous_pending = Payout::where('api_id', $api_key->id)
                ->where(function($query) {
                    $query->where('transfer_status', 1)
                        ->orWhere(function($subQuery) {
                            $subQuery->where('transfer_status', 2)
                                    ->where('status', 'Pending');
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


            // $payout->source = $source;
            // $payout->sign = $user_sign;
            $payout->api_id = $api_id;
            $payout->e_wallet_name = $request->e_wallet_name;
            $payout->amount = $request->amount;
            $payout->user_account_no = $request->user_account_no;

            if ($request->filled('partner_transection_id')) {
                $payout->partner_transection_id = $request->partner_transection_id;
            }
            if ($request->filled('member_id')) {
                $payout->member_id = $request->member_id;
            }


            $payout->save();

            $parentIds = ParentCommission::where('user_id', $api_key->id)
                ->pluck('parent_id')
                ->unique()
                ->values();
            foreach ($parentIds as  $parentId) {

                $parent_charge = 0;

                $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($parent_commission) {
                    $parent_charge = $parent_commission->withdrawal_percentage * $request->amount / 100;
                } else {
                    $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->withdrawal_percentage * $request->amount / 100;
                    }
                }

                if ($parent_charge > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $parentId;
                    $PartnerCommission->type = 2;
                    $PartnerCommission->amount = $request->amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount =  $request->amount + $charge;
                    $PartnerCommission->charges_p = $commissions->withdrawal_percentage ?? 0;
                    $profit_p = $parent_commission->withdrawal_percentage;
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
            $payout->save();



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

            if ($data->status == "Complete") {
                DB::rollBack();
                return response()->json(['message' => 'Payout Already Added']);
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

                    $data = Payout::where('id', $payout->payout_log_id)->first();
                    if ($data) {
                        $data->status = 'Complete';
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
                        'completion_date' => Carbon::parse($payout->date_time)->toDateString(),
                        'completion_time' => Carbon::parse($payout->date_time)->toTimeString(),
                        'created_at' => $payout->created_at,
                        'updated_at' => $payout->updated_at,
                        'sign' => $sign,
                    ];

                    if (!empty($payout->member_id)) {
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
                                if($parent_api_key){
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
                                $e_wallet_log_save->final_amount = ($payout_data->amount + $payout_data->fee - $payout_data->commission);
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
                    $data->transfer_status = 3;
                    $payout_data->save();

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
                            'completion_date' => Carbon::parse($payout->date_time)->toDateString(),
                            'completion_time' => Carbon::parse($payout->date_time)->toTimeString(),
                            'created_at' => $payout->created_at,
                            'updated_at' => $payout->updated_at,
                            'sign' => $sign,
                        ];

                        if (!empty($payout->member_id)) {
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
                }

                if ($commit == 0) {
                    DB::commit();
                }
            }

            if ($commit == 0) {
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

        $commit = 0;

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
                                if($parent_api_key){
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
                                $e_wallet_log_save->final_amount = ($payout_data->amount + $payout_data->fee - $payout_data->commission);
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
                    $payout_data->transfer_status = 3;
                    $payout_data->save();

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
                                'completion_date' => Carbon::parse($payout->date_time)->toDateString(),
                                'completion_time' => Carbon::parse($payout->date_time)->toTimeString(),
                                'created_at' => $payout->created_at,
                                'updated_at' => $payout->updated_at,
                                'sign' => $sign,
                            ];

                            if (!empty($payout->member_id)) {
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

                if ($commit == 0) {
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


    public function manualProcess(Request $request)
    {
        $request->validate([
            'original_id' => 'required|integer',
            'new_amount' => 'required|numeric|min:0',
            'type' => 'required|in:payment,payout',
        ]);

        // Determine model based on type
        $modelClass = match ($request->type) {
            'payment' => Payment::class,
            'payout' => Payout::class,
        };

        // Find original record
        $original = $modelClass::findOrFail($request->original_id);

        // Duplicate record and update amount
        $new = $original->replicate();
        $new->amount = $request->new_amount;
        $new->txn_id = $request->txn_amount;
        $new->status = 'Complete';
        $new->save();

        return response()->json(['success' => true, 'message' => 'Record duplicated successfully.']);
    }

    public function workboard(Request $request)
    {
        $EWalletAccount = EWalletAccount::where('status', 1)->get();
        $notifications = EWalletAccount::whereColumn('live_balance', '<', 'low_balance_amount')
        ->where('status',1)
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();
        $pending_list = Payout::where('updated_at', '<=', Carbon::now()->subMinutes(5))
        ->where('status','Pending')
        // ->where('check_by', 0)
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();

        if ($request->ajax()) {
            return response()->json([
                'ewallets' => $EWalletAccount,
                'notifications' => $notifications,
                'pending_list' => $pending_list,
                'user_id' => auth()->id(),
            ]);
        }

        $pageTitle = "Workboard";
        $apis = Api::get();
        return view('admin.payout.workboard', compact('pageTitle', 'apis'));
    }

    public function fetchrecords(Request $request){
        $query = $request->input('search');
        $source = $request->input('source');


    $payments = collect();
    $payouts = collect();
    if ($source === 'all' || $source === 'payment') {
        $payments = Payment::with('txn_record','api','eWalletAccount.location')->select(
            'id', 'amount', 'sender', 'e_wallet_phone_number', 'txn_id', 'e_wallet_type','callback','api_id',
            'sender','e_wallet_name', 'status', 'created_at','updated_at', 'partner_transection_id',
            'adjusted_by', DB::raw("'payment' as type")
        )
        ->latest('created_at')
        ->where('show_none', 0)
        ->when($query, function ($q) use ($query) {
            $q->where(function ($subQuery) use ($query) {
                $subQuery->where('txn_id', '=', $query)
                         ->orWhere('partner_transection_id', '=', $query);
            });
        })
        ->take(10)
        ->get();
    }

    if ($source === 'all' || $source === 'payout') {
    $payouts = Payout::select(
            'id', 'amount', 'status', 'created_at', 'partner_transection_id',
            'adjusted_by', DB::raw("'payout' as type")
        )
        ->latest('created_at')
        ->where('show_none', 0)
        ->when($query, function ($q) use ($query) {
            $q->where(function ($subQuery) use ($query) {
                $subQuery->where('txn_id', '=', $query)
                         ->orWhere('partner_transection_id', '=', $query);
            });
        })
        ->take(10)
        ->get();
    }


        $merged = $payments->merge($payouts);
        $mergedTransactions = $merged->sortByDesc('created_at')->values()->take(10);
        return response()->json([
            'transactions' => $mergedTransactions,
            'user_id' => auth()->id(),
        ]);
    }

    public function updatePayment(Request $request) {

        $this->validate($request, [
            'id' => 'required',
            'status' => ['required', Rule::in(['Complete', 'Reject'])],
        ]);
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

                    $payment = PendingPayment::where('txn_id', $request->txn_id)->orderBy('id', 'DESC')->first();
                    if ($payment) {
                        if ($payment->amount != $data->amount) {
                            throw new \Exception("Wrong TXN.");
                        }
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
                    $e_wallet_log_save->transaction_id = $data->id;
                    $e_wallet_log_save->account_id = $account->id;
                    $e_wallet_log_save->source = 'action';
                    $e_wallet_log_save->save();
                }

                $data->feedback = @$req['feedback'];

                $data->save();

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
                session()->flash('success', 'Approve Successfully');
            } elseif ($request->status == 'Reject') {
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
                session()->flash('success', 'Reject Successfully');

            }
            if($commit==0){
                DB::commit();
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return back();
        }

    }

    public function updatePayout(Request $request) {
        $payout = Payout::findOrFail($request->id);
        $payout->amount = $request->amount;
        $payout->status = 'Pending';
        $payout->save();
        return response()->json(['success' => true]);
    }



    public function adjustTransaction(Request $request)
{
    $model = $request->type === 'payment' ? Payment::class : Payout::class;
    $record = $model::findOrFail($request->id);
    $record->adjusted_by = auth()->id();
    $record->save();

    return response()->json(['success' => true]);
}


    public function hideTransaction(Request $request)
{
    $id = $request->input('id');
    $type = $request->input('type');

    if ($type === 'payment') {
        $record = Payment::find($id);
    } elseif ($type === 'payout') {
        $record = Payout::find($id);
    } else {
        return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
    }

    if ($record) {
        $record->show_none = 1;
        $record->save();
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Record not found'], 404);
}




    // Partner Commission

    public function apiCommissions(Request $request)
    {

        $from_date = $request->from_date ?? now()->toDateString();
        $to_date = $request->to_date ?? now()->toDateString();

        // Base query
        $recordsQuery = PartnerCommission::with([
            'api:id,name',
            'fromapi:id,name'
        ])
            ->where('status', 1)
            ->whereHas('api')
            ->whereHas('fromapi');

        // Default filter for today's records
        if (!empty($from_date) && !empty($to_date)) {
            $recordsQuery->whereDate('created_at', '>=', $from_date);
            $recordsQuery->whereDate('created_at', '<=', $to_date);
        } else {
            $recordsQuery->whereDate('created_at', now());
        }

        if (!empty($request->partner)) {
            $recordsQuery->where('api_id', $request->partner);
        }

        if (!empty($request->parent)) {
            $recordsQuery->where('from_id', $request->parent);
        }

        if (!empty($request->type) || $request->type == '0') {
            $recordsQuery->where('type', $request->type);
        }
        $TotalAmountSum = $recordsQuery->sum('amount');
        $TotalChargesSum = $recordsQuery->sum('charges');
        $TotalAAmountSum = $recordsQuery->sum('total_amount');
        $TotalProfitSum = $recordsQuery->sum('profit');
        // echo $TotalAmount.'<br>';
        // Paginate the results
        $records = $recordsQuery->orderBy('id', 'DESC')->paginate(50); // 10 items per page
        // Check if the current page is the last page
        $isLastPage = $records->currentPage() === $records->lastPage();

        $totalAmount = null;
        $totalChargesSum = null;
        $totalAAmountSum = null;
        $totalProfitSum = null;

        if ($isLastPage) {
            $totalAmount = number_format($TotalAmountSum, 2);
            $totalChargesSum = number_format($TotalChargesSum, 2);
            $totalAAmountSum = number_format($TotalAAmountSum, 2);
            $totalProfitSum = number_format($TotalProfitSum, 2);
        }
        // dd($recordsQuery->sum('amount'));
        $pageTitle = "Partners Commission History";
        $partners = Api::where('type', 'Admin')->get();

        return view('admin.payout.commission_report', compact('records', 'pageTitle', 'partners', 'from_date', 'to_date', 'totalAmount', 'isLastPage', 'totalChargesSum', 'totalAAmountSum', 'totalProfitSum'));
    }

    public function exportCommissions(Request $request)
    {
        $from_date = $request->from_date ?? now()->toDateString();
        $to_date = $request->to_date ?? now()->toDateString();
        $partner = $request->partner;
        $parent = $request->parent;
        $type = $request->type;

        return Excel::download(
            new PartnerCommissionExport($from_date, $to_date, $partner, $parent, $type),
            'commissions_report_' . now()->format('Ymd') . '.csv'
        );
    }


    public function adjustments()
{
    $pageTitle = "Partners Adjustments History";

    $firstDayOfMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
    $lastDayOfMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();
    $monthyear = Carbon::now()->subMonth()->startOfMonth();

    // Fetch all Admin partners at once
    $partners = Api::where('type', 'Admin')->get();
    $partnerIds = $partners->pluck('id');

    // Fetch all payments grouped by api_id
    $payments = Payment::where('status', 'Complete')
        ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
        ->whereIn('api_id', $partnerIds)
        ->selectRaw('api_id, COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
        ->groupBy('api_id')
        ->get()
        ->keyBy('api_id');

    // Fetch all payouts grouped by api_id
    $payouts = Payout::where('transfer_status', 2)
        ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
        ->whereIn('api_id', $partnerIds)
        ->selectRaw('api_id, COUNT(*) as fund_count, SUM(amount) as fund_sum, SUM(charge) as charge_sum')
        ->groupBy('api_id')
        ->get()
        ->keyBy('api_id');

    // Preload all commissions by category for faster access
    $commissionsByCategory = Commission::orderBy('to_amount', 'desc')->get()->groupBy('category_id');

    $adjustmentsToInsert = [];

    foreach ($partners as $partner) {
        if ($partner->website === env('APP_WEBSITE')) continue;

        $api_id = $partner->id;
        $category_id = $partner->category_id;

        if (empty($category_id) || !isset($commissionsByCategory[$category_id])) {
            continue;
        }

        $total_adjustment_amount = 0;
        $total_payment = 0;
        $total_payout = 0;

        // ===== Payment Side Calculation =====
        if (isset($payments[$api_id])) {
            $pay = $payments[$api_id];
            $commission = $commissionsByCategory[$category_id]
                ->firstWhere(fn($c) => $pay->fund_sum >= $c->from_amount && $pay->fund_sum <= $c->to_amount)
                ?? $commissionsByCategory[$category_id]->first(); // fallback to top one

            $charge = $commission ? $commission->deposit_percentage * $pay->fund_sum / 100 : 0;
            $get_adjustment = $pay->charge_sum - $charge;

            $total_adjustment_amount += $get_adjustment;
            $total_payment = $pay->fund_sum;
        }

        // ===== Payout Side Calculation =====
        if (isset($payouts[$api_id])) {
            $payout = $payouts[$api_id];
            $commission = $commissionsByCategory[$category_id]
                ->firstWhere(fn($c) => $payout->fund_sum >= $c->from_amount && $payout->fund_sum <= $c->to_amount)
                ?? $commissionsByCategory[$category_id]->first();

            $charge = $commission ? $commission->withdrawal_percentage * $payout->fund_sum / 100 : 0;
            $get_adjustment = $payout->charge_sum - $charge;

            $total_adjustment_amount += $get_adjustment;
            $total_payout = $payout->fund_sum;
        }

        // ===== Insert Adjustment If Not Exists =====
        if ($total_adjustment_amount > 0) {
            $existing = Adjustment::where('partner_id', $partner->id)
                ->whereMonth('month', $monthyear->month)
                ->whereYear('month', $monthyear->year)
                ->exists();

            if (!$existing) {
                $adjustmentsToInsert[] = [
                    'month' => $lastDayOfMonth,
                    'adjustment' => $total_adjustment_amount,
                    'payment' => $total_payment,
                    'payout' => $total_payout,
                    'partner_id' => $partner->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
    }

    // Bulk insert new adjustments
    if (!empty($adjustmentsToInsert)) {
        Adjustment::insert($adjustmentsToInsert);
    }

    // Load records for view
    $records = Adjustment::with('api')->orderBy('id', 'DESC')->get();

    return view('admin.payout.adjustments', compact('records', 'pageTitle', 'partners'));
}


    public function adjustmentSearch(Request $request)
    {

        $partners = Api::where('type', 'Admin')->get();

        $records = Adjustment::with('api');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $records->whereDate('month', '>=', $request->from_date);
            $records->whereDate('month', '<=', $request->to_date);
        } elseif (!empty($request->from_date)) {
            $records->whereDate('month', '>=', $request->from_date);
        } elseif (!empty($request->to_date)) {
            $records->whereDate('month', '<=', $request->to_date);
        }

        if (!empty($request->partner)) {
            $records->where('partner_id', $request->partner);
        }

        if (!empty($request->status) || $request->status == '0') {
            $records->where('status', $request->status);
        }

        $records = $records->orderBy('id', 'DESC')->get();

        $pageTitle = "Search Adjustment History";
        return view('admin.payout.adjustments', compact('records', 'pageTitle', 'partners'));
    }


    public function partnerBalance(Request $request)
    {
        $records = ApiTransaction::with('api')->orderBy('id', 'DESC')->paginate(20);
        $pageTitle = "Partners Adjustments";
        $partners = Api::where('type', 'Admin')->paginate(10);

        return view('admin.payout.partner_balance', compact('records', 'pageTitle', 'partners'));
    }

    public function partnerBalanceSearch(Request $request)
    {

        $partners = Api::where('type', 'Admin')->paginate(20);

        $records = ApiTransaction::with('api');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $records->whereDate('created_at', '>=', $request->from_date);
            $records->whereDate('created_at', '<=', $request->to_date);
        } elseif (!empty($request->from_date)) {
            $records->whereDate('created_at', '>=', $request->from_date);
        } elseif (!empty($request->to_date)) {
            $records->whereDate('created_at', '<=', $request->to_date);
        }

        if (!empty($request->partner)) {
            $records->where('partner_id', $request->partner);
        }

        if (!empty($request->adjustment) || $request->adjustment == '0') {
            $records->where('adjustment', $request->adjustment);
        }

        $records = $records->orderBy('id', 'DESC')->paginate(20);

        $pageTitle = "Search Partner Adjustments";
        return view('admin.payout.partner_balance', compact('records', 'pageTitle', 'partners'));
    }


    public function apilogs(Request $request)
    {
        $data = ApiLog::where('type', 'API')->orderBy('id', 'DESC')->paginate(20);
        $pageTitle = "API Logs";
        return view('admin.payout.apiLogs', compact('data', 'pageTitle'));
    }

    public function getApiLog($url)
{

    $log = \App\Models\ApiLog::where('request_url',  $url)->orderby('id','Desc')->get();

    if ($log) {
        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'No log found.'
    ]);
}
    public function getApiLog2($url)
{

    $log = \App\Models\ApiLog::where('request_payload',  $url)->orderby('id','Desc')->get();

    if ($log) {
        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'No log found.'
    ]);
}

    public function functionlogs(Request $request)
    {
        $data = ApiLog::where('type', 'Function')->orderBy('id', 'DESC')->paginate(20);
        $pageTitle = "API Logs";
        return view('admin.payout.functionLogs', compact('data', 'pageTitle'));
    }


    public function apisLgoin($id)
    {
        $api = Api::findOrFail($id);
        Auth::guard('partner')->login($api);

        return redirect()->route('partner.profile')->with('success', 'Login to Partner Dashboard.');
    }


    public function accountGroupList()
    {
        $data['methods'] = AccountGateway::orderBy('sort_by', 'asc')->get();
        $data['categories'] = Category::where('status', '1')->get();
        $data['pageTitle'] = 'Accounts Management';

        $this->updateLimits();

        $data['records'] = EWalletAccount::with(['apiHits' => function ($query) {
            $query->whereBetween('created_at', [now()->subSeconds(70), now()]);
        }])->paginate(20);

        foreach ($data['records'] as $record) {
            $record->live = $record->apiHits ? 1 : 0; // If relation exists, set live = 1
        }
        return view('admin.accounts.ewallet_accounts', $data);
    }


    public function addAccountPairs(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255|unique:groups,name',
            'pairs' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Save group
            $groupId = DB::table('groups')->insertGetId([
                'name' => $request->input('group_name'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Save account-group pairs if provided
            $pairs = $request->input('pairs', []);

            if (!empty($pairs)) {
                $accountGroups = [];

                foreach ($pairs as $accountId) {
                    $accountGroups[] = [
                        'account_id' => $accountId,
                        'group_id' => $groupId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('account_groups')->insert($accountGroups);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Group created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add account group: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the group.']);
        }
    }

    public function updateAccountGroup(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:groups,id',
            'edit_group_name' => 'required|string|max:255',
            'edit_pairs' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $group = Group::findOrFail($request->id);
            $group->name = $request->edit_group_name;
            $group->save();

            // Remove old entries
            AccountGroup::where('group_id', $group->id)->delete();

            // Add new ones if any
            if (!empty($request->edit_pairs)) {
                foreach ($request->edit_pairs as $account_id) {
                    AccountGroup::create([
                        'account_id' => (int)$account_id,
                        'group_id' => $group->id,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function updateaccountStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'status' => 'required|boolean', // 1 = on, 0 = off
            'type' => 'required',
        ]);

        $wallet = EWalletAccount::find($request->id);
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found.'], 404);
        }

        $currentType = strtolower($wallet->account_type ?? '');
        $newType = $request->type;
        $status = $request->status;

        $hasDeposit = in_array($currentType, ['deposit', 'both']);
        $hasWithdrawal = in_array($currentType, ['withdrawal', 'both']);

        if ($newType === 'deposit') {
            $wallet->account_type = $status
                ? ($hasWithdrawal ? 'Both' : 'Deposit')
                : ($hasWithdrawal ? 'Withdrawal' : '');
        } elseif ($newType === 'withdrawal') {
            $wallet->account_type = $status
                ? ($hasDeposit ? 'Both' : 'Withdrawal')
                : ($hasDeposit ? 'Deposit' : '');
        } elseif ($newType == 'status') {
            // ✅ Only update status column
            $wallet->status = $status;
        }

        $wallet->save();

        return response()->json([
            'success' => true,
            'account_type' => $wallet->account_type,
            'status' => $wallet->status,
        ]);
    }

    public function changeStatus($id)
    {
        $account = EWalletAccount::findOrFail($id);
        $account->status = $account->status == 1 ? 0 : 1;
        $account->save();


        if($account->status==1){

            $Setting = Setting::where('name', 'last_account_active')->first();
            $Setting->value = Carbon::now();
            $Setting->save();
        }

        return response()->json([
            'success' => true,
            'status' => $account->status,
            'message' => 'Status updated successfully.'
        ]);
    }
}
