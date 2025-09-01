<?php

namespace App\Http\Controllers\Admin;

// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Gateway;

use App\Models\Category;
use App\Models\AccountGroup;
use App\Models\UserLocation;

use Illuminate\Http\Request;
use App\Models\AccountGateway;
use App\Models\EWalletAccount;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $data['methods'] = Gateway::orderBy('sort_by', 'asc')->get();
        $data['categories'] = Category::all();
        $data['pageTitle'] = 'Accounts Management';
        $data['groups'] = AccountGroup::all();
        $this->updateLimits();

        $input_status = $request->filled('status') ? $request->status : null;
        $today = Carbon::today();

        $data['gateways'] = EWalletAccount::select('e_wallet_name')->distinct()->pluck('e_wallet_name')->toArray();

        // Payments Subquery
        $paymentsSubQuery = DB::table('payments')
            ->selectRaw('
        e_wallet_phone_number,
        COUNT(*) as today_transaction_count,
        SUM(amount) as today_total_deposit
        ')
            ->whereDate('created_at', $today)
            ->where('status', 'Complete')
            ->groupBy('e_wallet_phone_number');

        // Payouts Subquery
        $payoutsSubQuery = DB::table('payouts')
            ->selectRaw('
        e_wallet_phone_number,
        COUNT(*) as today_payout_count,
        SUM(amount) as today_total_payout
    ')
            ->whereDate('created_at', $today)
            ->where('status', 'Complete')
            ->groupBy('e_wallet_phone_number');

        $data['records'] = EWalletAccount::leftJoinSub($paymentsSubQuery, 'p', function ($join) {
            $join->on('e_wallet_accounts.account_no', '=', 'p.e_wallet_phone_number');
        })
            ->leftJoinSub($payoutsSubQuery, 'po', function ($join) {
                $join->on('e_wallet_accounts.account_no', '=', 'po.e_wallet_phone_number');
            })
            ->with([
                'apiHits' => function ($query) {
                    $query->whereBetween('created_at', [now()->subSeconds(70), now()]);
                },
                'location',
                'accountGroups.group'
            ])
           ->when($input_status !== null, function ($query) use ($input_status) {
                return $query->where('e_wallet_accounts.status', $input_status);
            })
            ->when($request->filled('gateway_input'), function ($query) use ($request) {
                $query->where('e_wallet_accounts.e_wallet_name', $request->gateway_input);
            })
            ->select(
                'e_wallet_accounts.*',
                DB::raw('COALESCE(p.today_transaction_count, 0) as today_transaction_count'),
                DB::raw('COALESCE(p.today_total_deposit, 0) as today_total_deposit'),
                DB::raw('COALESCE(po.today_payout_count, 0) as today_payout_count'),
                DB::raw('COALESCE(po.today_total_payout, 0) as today_total_payout')
            )
            ->paginate(1000);

        return view('admin.accounts.ewallet_accounts', $data);
    }

    public function availableaccounts()
    {
        $current_time = Carbon::now('Asia/Dhaka');

        $eWallets = ['nagad', 'bkash', 'rocket'];
        $result = [];

        foreach ($eWallets as $ewallet) {
            $accountsData = [];

            // Step 1: All active accounts
            $all_active_accounts = EWalletAccount::where('e_wallet_name', $ewallet)
                ->where('status', 1)
                ->whereIn('account_type', ['Deposit', 'Both'])
                ->with('timeSlots')
                ->get();

            foreach ($all_active_accounts as $acc) {
                $accountsData[$acc->account_no] = [
                    'account_no' => $acc->account_no,
                    'e_wallet' => $acc->e_wallet_name,
                    'active_status' => 'yes',
                    'available_limit_accounts' => 'no',
                    'time_slot_accounts' => 'no',
                    'final_active_accounts' => 'no',
                    'daily_remaining' => null,
                    'per_minute_remaining' => null,
                    'last_used' => $acc->d_last_used,
                ];
            }

            // Step 2: Apply monthly/daily transactions conditions
            $available_limit_accounts = $all_active_accounts->filter(function ($acc) {
                return $acc->monthly_limit > $acc->monthly_received &&
                    $acc->daily_limit_transaction > $acc->d_today_count &&
                    $acc->monthly_limit_transaction > $acc->d_month_count;
            })->values();

            foreach ($available_limit_accounts as $acc) {
                if (!isset($accountsData[$acc->account_no])) {
                    $accountsData[$acc->account_no] = [
                        'account_no' => $acc->account_no,
                        'e_wallet' => $acc->e_wallet_name,
                    ];
                }
                $accountsData[$acc->account_no]['available_limit_accounts'] = 'yes';
            }

            // Step 3: Apply time slot condition
            $time_slot_accounts = $available_limit_accounts->filter(function ($acc) use ($current_time) {
                return $acc->timeSlots->contains(function ($slot) use ($current_time) {
                    $from = Carbon::parse($slot->from_time);
                    $to = Carbon::parse($slot->to_time);
                    return $current_time->between($from, $to);
                });
            })->values();

            foreach ($time_slot_accounts as $acc) {
                if (!isset($accountsData[$acc->account_no])) {
                    $accountsData[$acc->account_no] = [
                        'account_no' => $acc->account_no,
                        'e_wallet' => $acc->e_wallet_name,
                    ];
                }
                $accountsData[$acc->account_no]['time_slot_accounts'] = 'yes';
            }

            // Step 4: Apply last used + per minute transaction conditions
            $final_active_accounts = $time_slot_accounts->filter(function ($acc) use ($current_time) {
                $lastUsed = Carbon::parse($acc->d_last_used);

                if ($lastUsed->diffInSeconds($current_time) < 60) {
                    return $acc->max_transaction_per_minute > $acc->d_one_min_count;
                }

                return $acc->max_transaction_per_minute > 0;
            })->map(function ($acc) use ($current_time) {
                $lastUsed = Carbon::parse($acc->d_last_used);

                $remainingDailyLimit = $acc->daily_limit - $acc->daily_received;

                if ($lastUsed->diffInSeconds($current_time) < 60) {
                    $remainingPerMinute = $acc->max_amount_per_minute - $acc->d_one_min_sum;
                } else {
                    $remainingPerMinute = $acc->max_amount_per_minute;
                }

                return [
                    'acc' => $acc,
                    'remainingDailyLimit' => $remainingDailyLimit,
                    'remainingPerMinute' => $remainingPerMinute,
                ];
            })->values();

            foreach ($final_active_accounts as $fa) {
                $acc = $fa['acc'];
                if (!isset($accountsData[$acc->account_no])) {
                    $accountsData[$acc->account_no] = [
                        'account_no' => $acc->account_no,
                        'e_wallet' => $acc->e_wallet_name,
                    ];
                }

                $accountsData[$acc->account_no]['final_active_accounts'] = 'yes';
                $accountsData[$acc->account_no]['daily_remaining'] = $fa['remainingDailyLimit'];
                $accountsData[$acc->account_no]['per_minute_remaining'] = $fa['remainingPerMinute'];
                $accountsData[$acc->account_no]['last_used'] = $acc->d_last_used;
            }

            $result[$ewallet] = $accountsData;
        }

        $pageTitle = "Available Accounts";
        return view('admin.accounts.available', compact('pageTitle','result'));
    }


    public function availableaccounts_old(){
        $current_time = Carbon::now('Asia/Dhaka');

        $eWallets = ['nagad', 'bkash', 'rocket'];
        $result = [];

        foreach ($eWallets as $ewallet) {
            // Step 1: All active accounts
            $all_active_accounts = EWalletAccount::where('e_wallet_name', $ewallet)
                ->where('status', 1)
                ->whereIn('account_type', ['Deposit', 'Both'])
                ->with('timeSlots')
                ->get();

            // Step 2: Apply monthly/daily transactions conditions
            $available_limit_accounts = $all_active_accounts->filter(function ($acc) {
                return $acc->monthly_limit > $acc->monthly_received &&
                    $acc->daily_limit_transaction > $acc->d_today_count &&
                    $acc->monthly_limit_transaction > $acc->d_month_count;
            })->values();

            // Step 3: Apply time slot condition
            $time_slot_accounts = $available_limit_accounts->filter(function ($acc) use ($current_time) {
                $validTimeSlot = $acc->timeSlots->contains(function ($slot) use ($current_time) {
                    $from = Carbon::parse($slot->from_time);
                    $to = Carbon::parse($slot->to_time);
                    return $current_time->between($from, $to);
                });
                return $validTimeSlot;
            })->values();

            // Step 4: Apply last used + per minute transaction conditions
            $final_active_accounts = $time_slot_accounts->filter(function ($acc) use ($current_time) {
                $lastUsed = Carbon::parse($acc->d_last_used);

                if ($lastUsed->diffInSeconds($current_time) < 60) {
                    return $acc->max_transaction_per_minute > $acc->d_one_min_count;
                }

                return $acc->max_transaction_per_minute > 0;
            })->map(function ($acc) use ($current_time) {
                $lastUsed = Carbon::parse($acc->d_last_used);

                // baki daily limit
                $remainingDailyLimit = $acc->daily_limit - $acc->daily_received;

                // baki per-minute limit
                if ($lastUsed->diffInSeconds($current_time) < 60) {
                    $remainingPerMinute = $acc->max_amount_per_minute - $acc->d_one_min_sum;
                } else {
                    $remainingPerMinute = $acc->max_amount_per_minute;
                }

                return [
                    'account_no' => $acc->account_no,
                    'e_wallet' => $acc->e_wallet_name,
                    'daily_remaining' => $remainingDailyLimit,
                    'per_minute_remaining' => $remainingPerMinute,
                    'last_used' => $acc->d_last_used,
                ];
            })->values();

            // Collect result for this wallet
            $result[$ewallet] = [
                'all_active_accounts' => $all_active_accounts->pluck('account_no'),
                'available_limit_accounts' => $available_limit_accounts->pluck('account_no'),
                'time_slot_accounts' => $time_slot_accounts->pluck('account_no'),
                'final_active_accounts' => $final_active_accounts,
            ];
        }

        dd($result);


            
        $pageTitle = "Available Accounts";
        return view('admin.accounts.available', compact('pageTitle'));
    }

    public function addAccount(Request $request)
    {
        $pageTitle = __('accounts.add_new_account');
        $categories = Category::select('name', 'id')->get();
        $methods = Gateway::select('name', 'id')->where('status', 1)->get();
        $groups = Group::all();
        $users_locations = UserLocation::where('status', 1)->get();
        return view('admin.accounts.add_account', compact('pageTitle', 'categories', 'methods', 'groups', 'users_locations'));
    }

    public function editAccount(Request $request, $id)
    {
        $pageTitle = __('accounts.edit_new_account');
        $categories = Category::select('name', 'id')->get();
        $methods = Gateway::select('name', 'id')->where('status', 1)->get();
        $groups = Group::all();
        $users_locations = UserLocation::where('status', 1)->get();

        $e_wallet_account = EWalletAccount::findOfFail($id);

        return view('admin.accounts.edit_account', compact('pageTitle', 'categories', 'methods', 'groups', 'users_locations', 'e_wallet_account'));
    }

    public  function  addCategory(Request $request)
    {
        $pageTitle = __('accounts.categories_list');
        // $categories = Category::select('name' , 'id' , 'status')->get();

        if (request()->ajax()) {
            $categories = Category::orderBy('id', 'DESC');

            return DataTables::of($categories)
                ->addIndexColumn()

                ->addColumn('status', function ($category) {
                    $statusClass = $category->status == 1 ? 'bg-success' : 'bg-danger';
                    $statusText = $category->status == 1 ? "Active" : 'Deactive';

                    return '<span class="toggle-status" data-id="' . $category->id . '" style="cursor:pointer;">' . ($category->status == 1 ? '<span class="badge bg-success">' . __('accounts.active') . '</span>' : '<span class="badge bg-danger">' . __('accounts.inactive') . '</span>') . '</span>';
                })
                ->addColumn('action', function ($category) {
                    return view('admin.accounts.partials.location-actions', compact('category'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.accounts.add_category', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        // Use manual validation to return JSON on failure
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json([
            'success' => 'true',
            'message' => 'Category created successfully.',
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'edit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($id),
            ],
            'edit_status' => 'required|boolean',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        $category->update([
            'name' => $request->input('edit_name'),
            'status' => $request->input('edit_status'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }


    public function destroy(Request $request)
    {
        $id = (int)$request->input('id');
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }


    public function gateway()
    {
        $pageTitle = __('accounts.gateways');
        if (request()->ajax()) {
            $gateways = Gateway::orderBy('id', 'DESC');

            return DataTables::of($gateways)
                ->addIndexColumn()
                ->editColumn('status', function ($gateways) {
                    $toggleRoute = route('admin.accounts.payment.methods.deactivate', $gateways->id);

                    return '<span class="toggle-status"
                                data-id="' . $gateways->id . '"
                                data-url="' . $toggleRoute . '"
                                style="cursor: pointer;">
                                ' . ($gateways->status == 1 ? '<span class="badge bg-success">' . __('accounts.active') . '</span>' : '<span class="badge bg-danger">' . __('accounts.inactive') . '</span>') . '
                            </span>';
                })
                ->addColumn('action', function ($gateway) {
                    return view('admin.accounts.partials.gateway-actions', compact('gateway'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $categories = Category::all();
        return view('admin.accounts.add_gateway', compact('pageTitle', 'categories'));
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

                $e_wallet_account->d_today_count = 0;
                $e_wallet_account->w_today_count = 0;
            }
            if (date('m', strtotime($e_wallet_account->last_limit_reset)) != $thisMonth) {
                $e_wallet_account->monthly_received = 0;
                $e_wallet_account->monthly_sent = 0;

                $e_wallet_account->d_month_count = 0;
                $e_wallet_account->w_month_count = 0;
            }
            $e_wallet_account->last_limit_reset = $todayDate;
            $e_wallet_account->save();
        }
    }

    public function changeStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();
        return response()->json([
            'success' => true,
            'status' => $category->status,
        ]);
    }

    public function getAccountsByCategory($category_id)
    {
        $accounts = Gateway::where('category_id', $category_id)
            ->where('status', 1)
            ->get(['id', 'name', 'currency']);

        return response()->json($accounts);
    }

    public function toggleStatus(Request $request)
    {
        $account = Gateway::find($request->id);

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Gateway not found.']);
        }

        $account->status = $request->status;
        $account->save();

        // Add audit log
        \App\Models\AuditLog::create([
            'user_id'     => auth()->id(),
            'module'      => 'EWalletAccount Account Management',
            'module_id'   => $account->id,
            'description' => auth()->user()->name . ' ' . ($account->status ? 'activated' : 'deactivated') . ' eWallet account ( ' . $account->name . ')',
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }


    public function onOffAccount()
    {
        $pageTitle = __('accounts.on_off_account');
        // $records = EWalletAccount::with(['apiHits' => function ($query) {
        //     $query->whereBetween('created_at', [now()->subSeconds(70), now()]);
        // }, 'location', 'accountGroups.group'])->paginate(1000);

        // foreach ($records as $record) {
        //     $record->live = $record->apiHits ? 1 : 0;
        // }

        $records =  Gateway::paginate(50);


        return view('admin.accounts.on_off_account', compact('pageTitle', 'records'));
    }

    public function updateAccountType(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:e_wallet_accounts,id',
            'account_type' => 'nullable|in:Deposit,Withdrawal,Both',
        ]);

        $wallet = EWalletAccount::findOrFail($request->id);
        $oldType = $wallet->account_type;

        $wallet->account_type = $request->account_type;
        $wallet->save();

        // Log the change
        \App\Models\AuditLog::create([
            'user_id'     => auth()->id(),
            'module'      => 'EWalletAccount Account Management',
            'module_id'   => $wallet->id,
            'description' => auth()->user()->name . " changed account type from '{$oldType}' to '{$wallet->account_type}' for eWallet account ( {$wallet->e_wallet_name})",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account type updated successfully!',
        ]);
    }
    public function updateGatewayDeposit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:gateways,id',
        ]);

        $wallet = Gateway::findOrFail($request->id);
        $oldType = $wallet->deposit_on;

        $wallet->deposit_on = $request->status;
        $wallet->save();

        // Log the change
        \App\Models\AuditLog::create([
            'user_id'     => auth()->id(),
            'module'      => 'Gateway Deposit Status Changed',
            'module_id'   => $wallet->id,
            'description' => auth()->user()->name . " changed gateway deposit from '{$oldType}' to '{$wallet->deposit_on}' for gateway ( {$wallet->name})",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gateway Deposit status updated successfully!',
        ]);
    }
    public function updateGatewayWithdrawal(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:gateways,id',
        ]);

        $wallet = Gateway::findOrFail($request->id);
        $oldType = $wallet->withdrawal_on;

        $wallet->withdrawal_on = $request->status;
        $wallet->save();

        // Log the change
        \App\Models\AuditLog::create([
            'user_id'     => auth()->id(),
            'module'      => 'Gateway Withdrawal Status Changed',
            'module_id'   => $wallet->id,
            'description' => auth()->user()->name . " changed gateway withdrawal from '{$oldType}' to '{$wallet->withdrawal_on}' for gateway ( {$wallet->name})",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gateway Withdrawal status updated successfully!',
        ]);
    }

    public function sendGatewayNotice(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:gateways,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $gatewayId = $request->input('id');
            $gateway = Gateway::find($gatewayId);

            if (!$gateway) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gateway not found.'
                ], 404);
            }

            // Telegram Setup
            $support_chat_id = "-4683359325";
            $botToken_support = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
            $url_support = "https://api.telegram.org/bot{$botToken_support}/sendMessage";

            // Format status
            $status = $gateway->status == 1 ? 'Active' : 'Inactive';
            $depositStatus = $gateway->deposit_on == 1 ? 'Active' : 'Inactive';
            $withdrawalStatus = $gateway->withdrawal_on == 1 ? 'Active' : 'Inactive';

            // Telegram Message (escaped properly)
            $message_support = "*Gateway Log*\n\n";
            $message_support .= "*Gateway Name:* `{$gateway->name}`\n";
            $message_support .= "*Status:* `{$status}`\n";
            $message_support .= "*Deposit Status:* `{$depositStatus}`\n";
            $message_support .= "*Withdrawal Status:* `{$withdrawalStatus}`\n";

            // Send Telegram message
            $response = Http::post($url_support, [
                'chat_id' => $support_chat_id,
                'text' => $message_support,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send Telegram message.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Notice sent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
