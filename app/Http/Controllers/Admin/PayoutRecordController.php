<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api;
use App\Models\ApiHit;
use App\Models\Commission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EWalletAccount;
use App\Models\ApiTransaction;
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
use App\Models\Settlement;
use App\Http\Traits\Upload;
use App\Models\SmsLog;
use App\Models\EWalletTransfer;

class PayoutRecordController extends Controller
{
    use Upload;

    public function apis(Request $request)
    {
        $records = Api::where('type', 'Admin')->select(['id', 'name', 'username', 'email', 'phone', 'acc_type', 'website','api_endpoint_deposit', 'api_endpoint_withdrawal', 'redirect_url','api_key', 'secret_key', 'balance', 'min_deposit', 'min_withdrawal', 'status'])->paginate(20);
        $pageTitle = "Manage APIs";
        return view('admin.payout.api', compact('records', 'pageTitle'));
    }

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




}
