<?php

namespace App\Http\Controllers\Partner;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Log;
use App\Models\Txn;
use App\Models\Fund;
use App\Models\User;
use App\Models\ApiHit;
use App\Models\Payout;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Signature;
use App\Models\Commission;
use App\Models\PartnerLog;
use App\Models\Settlement;
use Carbon\CarbonTimeZone;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\PayoutMethod;


use Illuminate\Http\Request;
use App\Models\ApiTransaction;
use App\Models\EWalletAccount;
use App\Services\BasicService;
use App\Models\MerchantAccount;
use Illuminate\Validation\Rule;
use App\Models\PartnerCommission;
use App\Models\EWalletTransaction;
use App\Models\TransactionSetting;
use Illuminate\Support\Facades\DB;
use App\Models\DailyPartnerSummary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\DailyPartnerSummaryLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log as LaravelLog;


class PayoutRecordController extends Controller
{
    public function methods($username)
    {
        $open_user = API::where('username', $username)->first();
        if ($open_user && $open_user->type == "Admin") {
            return view('partner.payout.methods', compact('username'));
        } else {
            abort(404);
        }
    }

    public function depositFund($username)
    {
        if (session()->has('txn_verified')) {
            session()->forget('txn_verified');
        }
        $open_user = API::where('username', $username)->first();
        if ($open_user && $open_user->type == "Admin") {
            $min_deposit = $open_user->min_deposit;
            if (session()->get('plan_id') != null) {
                return redirect(route('user.payment'));
            }
            $totalPayment = null;
            $gateways = Gateway::where('status', 1)->orderBy('sort_by', 'ASC')->get();
            return view('partner.payout.depositFund', compact('totalPayment', 'gateways', 'username', 'min_deposit'));
        } else {
            abort(404);
        }

    }


    public function payoutMoneyTransection($username)
    {
        $open_user = API::where('username', $username)->first();
        if ($open_user && $open_user->type == "Admin") {
            $min_withdrawal = $open_user->min_withdrawal;
            $title = "Payout Money";
            $gateways = PayoutMethod::whereStatus(1)->get();
            return view('partner.payout.moneyopen', compact('title', 'gateways', 'username','min_withdrawal'));
        } else {
            abort(404);
        }
    }




    public function addFundRequestOpen(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = validator()->make($request->all(), [
                'gateway' => 'required',
                'amount' => 'required',
                'username' => 'required'
            ]);
            if ($validator->fails()) {
                return response($validator->messages(), 422);
            }
            $open_user = API::where('username', $request->username)->lockForUpdate()->first();
            if (!$open_user || $open_user->type != "Admin") {
                DB::rollBack();
                return response()->json(['error' => 'Contact with Admin or your link provider'], 422);
            }

            $user_account_no = "";
            if($open_user->txn_verification==0){
                $acc = $request->account_no;
                $ewalletee = strtolower($request->gateway);

                if (!is_numeric($acc)) {
                        return response()->json(['code'=>605, 'error' => 'Account number formate not valid'], 404);
                    }

                    if (substr($acc, 0, 2) === "01") {
                        $num_digits = strlen($acc);
                        if ($ewalletee == 'bkash' && $num_digits!=11) {
                            DB::rollBack();
                            return response()->json(['code'=>605, 'error' => 'Account number should be 11 digit'], 404);
                        }
                        if ($ewalletee == 'nagad' && $num_digits!=11) {
                            DB::rollBack();
                            return response()->json(['code'=>605, 'error' => 'Account number should be 11 digit'], 404);
                        }
                        if ($ewalletee == 'rocket' && ($num_digits<11 || $num_digits>12)) {
                            DB::rollBack();
                            return response()->json(['code'=>605, 'error' => 'Account number should be 11 or 12 digit'], 404);
                        }
                    } else {
                        DB::rollBack();
                        return response()->json(['code'=>605, 'error' => 'Account number should start from 01'], 404);
                    }

                $user_account_no = $request->account_no;
            }elseif($request->filled('account_no')){
                $user_account_no = $request->account_no;
            }

            if ($open_user->min_deposit > $request->amount) {
                DB::rollBack();
                return response()->json(['error' => 'Min Deposit Limit is ' . $open_user->min_deposit], 422);
            }

            $basic = (object)config('basic');
            $gate = Gateway::where('code', $request->gateway)->where('status', 1)->first();
            if (!$gate) {
                DB::rollBack();
                return response()->json(['error' => 'Invalid Gateway'], 422);
            }

            $this->updateLimits();
            $this->updateEWallets();

            $current_time = Carbon::now('Asia/Dhaka');

                $Setting = Setting::where('name', 'last_account_active')->first();

                $recordcounts = Fund::where('gateway_id', $gate->id)
                    ->where('created_at', '>=', $Setting->value)
                    ->select('e_wallet_phone_number', DB::raw('count(*) as total'))
                    ->groupBy('e_wallet_phone_number')
                    ->pluck('total', 'e_wallet_phone_number')
                    ->toArray();

                $account = EWalletAccount::where('e_wallet_name', $gate->name)
                    ->where('monthly_limit', '>', 'monthly_received')
                    ->whereRaw('daily_limit - daily_received > ?', [$request->amount])
                    ->where('status', 1)
                    ->whereIn('account_type', ['Deposit', 'Both'])
                    ->where(function ($query) use ($current_time) {
                        $query->where('apply_time_limit', 0)
                            ->orWhere(function ($query) use ($current_time) {
                                $query->where('apply_time_limit', 1)
                                    ->where('from_time', '<=', $current_time)
                                    ->where('to_time', '>=', $current_time);
                            });
                    })
                    ->get()
                    ->sortBy(function ($single_account) use ($recordcounts) {
                        return $recordcounts[$single_account->account_no] ?? 0;
                    })
                    ->values()->first();


            if (!$account) {
                DB::rollBack();
                return response()->json(['error' => 'You Can not Proceed With this E-wallet account'], 422);
            }

            $reqAmount = $request->amount;
            if ($gate->min_amount > $reqAmount || $gate->max_amount < $reqAmount) {
                DB::rollBack();
                return response()->json(['error' => 'Please Follow Transaction Limit'], 422);
            }

            $user = $open_user;
            $source = $user->website;
            $api_id = $user->id;

            $sum = Payment::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('api_id', $api_id)
                ->where('status', 'Complete')
                ->sum('amount');

            $charge = 0;
            $commissions = Commission::where('api_id', $user->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $request->amount / 100;
            } else {
                $commissions = Commission::where('api_id', $user->id)->orderBy('to_amount', 'desc')->first();
                if ($commissions) {
                    $charge = $commissions->deposit_percentage * $request->amount / 100;
                }
            }

            $payable = getAmount($reqAmount - $charge);
            $final_amo = getAmount($payable * $gate->convention_rate);
            $account_no = $user_account_no;
            $e_wallet_phone_number = $account->account_no;

            $fund = $this->newFundOpen($request, $gate, $charge, $final_amo, $reqAmount, $account_no, $open_user, $e_wallet_phone_number);

            if ($charge > 0 && $user->parent_id > 0) {
                // $parent_commissions = Commission::where('id', $commissions->parent_id)->first();
                if($commissions->parent_id>0 && $commissions->parent_deposit_percentage>0){
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $user->id;
                    $PartnerCommission->from_id = $user->parent_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $request->amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $request->amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage;
                    $profit_p = $commissions->parent_deposit_percentage;
                    $profit = $profit_p * $request->amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();

                    // $main_parent_commissions = Commission::where('id', $parent_commissions->parent_id)->first();

                }

                if($commissions->parent2_id>0 && $commissions->parent2_deposit_percentage>0) {
                        $PartnerCommission = new PartnerCommission();
                        $PartnerCommission->api_id = $user->id;
                        $PartnerCommission->from_id = $commissions->parent2_id;
                        $PartnerCommission->type = 1;
                        $PartnerCommission->amount = $request->amount;
                        $PartnerCommission->charges = $charge;
                        $PartnerCommission->total_amount = $request->amount - $charge;
                        $PartnerCommission->charges_p = $commissions->deposit_percentage;
                        $profit_p = $commissions->parent2_deposit_percentage;
                        $profit = $profit_p * $request->amount / 100;
                        $PartnerCommission->profit = $profit;
                        $PartnerCommission->profit_p = $profit_p;
                        $PartnerCommission->transaction_id = $fund['id'];
                        $PartnerCommission->status = 0;
                        $PartnerCommission->save();
                }

            }

            //start
            $commit = 0;
            if ($fund) {
                $order = Payment::where('id', $fund['id'])->first();
                $payment = Payment::where('e_wallet_name', $gate->code)
                    ->where('amount', $fund['amount'])
                    ->where('sender', $fund['account_no'])
                    ->where('created_at', '>=', Carbon::now()->subHours(2))
                    ->where('status', 'Pending')
                    ->orderBy('id', 'DESC')
                    ->first();
                    if(!$payment){
                        $payment = Payment::where('e_wallet_name', $gate->code)
                        ->where('amount', $fund['amount'])
                        ->where(function ($query) use ($fund) {
                            $accountNo = (string)$fund['account_no']; // Convert number to string
                            $query->where('sender', 'LIKE', substr($accountNo, 0, 4) . '%')
                                ->where('sender', 'LIKE', '%' . substr($accountNo, -3))
                                ->where('sender', 'LIKE', '%XXXX%'); // Check if sender contains 'XXXX'
                        })
                        ->where('created_at', '>=', Carbon::now()->subHours(2))
                        ->where('status', 'Pending')
                        ->where('mac_address', '111.111.11.111')
                        ->orderBy('id', 'DESC')
                        ->first();
                        if($payment){
                            $payment->sender = $fund['account_no'];
                        }
                    }
                if ($payment) {
                    $net_amount = $reqAmount - $charge;
                    $open_user->balance += $net_amount;
                    $open_user->save();

                    $Log = new Log();
                    $Log->date_time = $payment->updated_at;
                    $Log->final_amount = $net_amount;
                    $Log->balance = $open_user->balance;
                    $Log->transection_type = 1;
                    $Log->transection_id = $payment->id;
                    $Log->partner_id = $open_user->id;
                    $Log->source = 'PartnerLink';
                    $Log->save();

                    $payment->source = $source;
                    $payment->api_id = $api_id;
                    $payment->charge = $charge;
                    $payment->status = 'Complete';
                    $payment->transaction_id = $fund['id'];
                    $payment->partner_transection_id = $fund['partner_transection_id'];
                    $payment->member_id = $fund['member_id'];
                    $payment->completion_at = $order->created_at;
                    $payment->trans_complete_date = Carbon::now();
                    $payment->completed_source = 'PartnerLink';
                    $payment->save();

                    $order->status = 1;
                    $order->completion_at = $order->created_at;
                    $order->trans_completed_date = Carbon::now();
                    $order->api_id = $api_id;
                    $order->payment_id = $payment->id;
                    $order->save();

                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $order->api_id)->whereDate('created_at','>=', $order->created_at)->get();
                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                        $amount_to_update = round($amount_to_update, 2);
                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                        $DailyPartnerSummary_record->save();

                        $summary_log = new DailyPartnerSummaryLog();
                        $summary_log->partner_id = $open_user->id;
                        $summary_log->partner_balance = $open_user->balance;
                        $summary_log->payment_id = $payment->id;
                        $summary_log->total_amount = $net_amount;
                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                        $summary_log->source = 'PartnerLink';
                        $summary_log->save();

                    }

                    $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                    foreach($PartnerCommissions as $PartnerCommission) {
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
                        $Log->source = 'PartnerLink';
                        $Log->save();

                        $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at','>=', $PartnerCommission->created_at)->get();
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
                            $summary_log->source = 'PartnerLink';
                            $summary_log->save();

                        }
                    }

                    $commit = 1;
                    DB::commit();

                    if (!empty($open_user->api_endpoint_deposit) && $open_user->website != env('APP_WEBSITE')) {

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
                                    'sign' => $order->sign,
                        ];

                        if(!empty($payment->member_id)){
                            $array_data['member_id'] = $payment->member_id;
                        }

                        $requestData = [
                            'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                            'request_url' => $open_user->api_endpoint_deposit,
                            'request_payload' => json_encode($array_data),
                            'request_headers' => json_encode([
                                'Content-Type' => 'application/json',
                                'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $logId = DB::table('api_logs')->insertGetId($requestData);
                        try {
                            $csrfToken = csrf_token();
                            $response = Http::withHeaders([
                                'Content-Type' => 'application/json',
                                'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                            ])
                                ->post($open_user->api_endpoint_deposit, $array_data);

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
            }
            //end

            session()->put('track', $fund['transaction']);
            session()->put('sender', $account->account_no);
            session()->put('account_no', $user_account_no);
            session()->put('processing_show', 0);
            session()->put('username', $request->username);


            if (1000 > $fund->gateway->id) {
                $method_currency = (checkTo($fund->gateway->currencies, $fund->gateway_currency) == 1) ? 'USD' : $fund->gateway_currency;
                $isCrypto = (checkTo($fund->gateway->currencies, $fund->gateway_currency) == 1) ? true : false;
            } else {
                $method_currency = $fund->gateway_currency;
                $isCrypto = false;
            }
            if($commit==0){
                DB::commit();
            }
            return [
                'gateway_image' => getFile(config('location.gateway.path') . $gate->image),
                'amount' => getAmount($fund->amount) . ' ' . $basic->currency_symbol,
                'charge' => getAmount($fund->charge) . ' ' . $basic->currency_symbol,
                'gateway_currency' => trans($fund->gateway_currency),
                'payable' => getAmount($fund->amount + $fund->charge) . ' ' . $basic->currency_symbol,
                'conversion_rate' => 1 . ' ' . $basic->currency . ' = ' . getAmount($fund->rate) . ' ' . $method_currency,
                'in' => trans('In') . ' ' . $method_currency . ':' . getAmount($fund->final_amount, 2),
                'isCrypto' => $isCrypto,
                'conversion_with' => ($isCrypto) ? trans('Conversion with') . $fund->gateway_currency . ' ' . trans('and final value will Show on next step') : null,
                'payment_url' => route('partner.addFund.processPayment.open'),
                'qr_image' => $account->image ? getFile(config('location.withdraw.path') . $account->image) : '',
                'sender' => $account->account_no,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }


    public function payoutMoneyRequestTransection(Request $request)
    {
        $this->validate($request, [
            'gateway' => 'required|integer',
            'username' => 'required',
            'amount' => ['required', 'numeric']
        ]);

        $open_user = API::where('username', $request->username)->first();
        if (!$open_user || $open_user->type != "Admin") {
            abort(404);
        }

        $min_withdrawal = $open_user->min_withdrawal;


        $basic = (object)config('basic');
        $method = PayoutMethod::where('id', $request->gateway)->where('status', 1)->firstOrFail();

        $authWallet = $open_user;

        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);

        $finalAmo = $request->amount + $charge;

        if ($request->amount < $min_withdrawal) {
            session()->flash('error', 'Minimum payout Amount ' . round($min_withdrawal, 2) . ' ' . $basic->currency);
            return back();
        }
        if ($request->amount > $method->maximum_amount) {
            session()->flash('error', 'Maximum payout Amount ' . round($method->maximum_amount, 2) . ' ' . $basic->currency);
            return back();
        }


        $pending_payout_ids = Payout::where('api_id', $open_user->id)
                ->where('status', 'Pending')
                ->whereNotNull('payout_log_id')
                ->where('payout_log_id', '!=', '')
                ->pluck('payout_log_id');

            $previous_pending = PayoutLog::where('api_id', $open_user->id)
                ->where(function ($query) use ($pending_payout_ids) {
                    $query->whereIn('status', [0, 1])
                        ->orWhere(function ($subQuery) use ($pending_payout_ids) {
                            $subQuery->where('status', 2)
                                    ->whereIn('id', $pending_payout_ids);
                        });
                })
                ->sum('amount');




        if ($finalAmo + $previous_pending > $authWallet['balance']) {
            if($previous_pending>0){
                session()->flash('error', 'You have already requested a withdrawal of '.round($previous_pending, 2).', which is still in process. Your remaining balance is '.round($authWallet['balance'] - $previous_pending, 2).'.');
                return back();
            }else{
                session()->flash('error', 'Insufficient balance' . snake2Title(round($authWallet['balance'], 2)) . ' For Withdraw.');
                return back();
            }

        } else {
            $trx = strRandom();
            $withdraw = new PayoutLog();
            $withdraw->user_id = 0;
            $withdraw->method_id = $method->id;
            $withdraw->amount = getAmount($request->amount);
            $withdraw->charge = $charge;
            $withdraw->net_amount = $finalAmo;
            $withdraw->trx_id = $trx;
            $withdraw->status = 0;
            $withdraw->api_key = $authWallet['api_key'];
            $withdraw->api_id = $authWallet['id'];
            $withdraw->save();
            session()->put('wtrx', $trx);
            session()->put('username', $request->username);
            return redirect()->route('partner.payout.preview.transection');
        }
    }

    public function updateLimits()
    {
        $todayDate = date('Y-m-d');
        $thisMonth = date('m');
        $e_wallet_accounts = EWalletAccount::select('last_limit_reset','daily_received','daily_sent','monthly_received','monthly_sent')->get();
        foreach ($e_wallet_accounts as $e_wallet_account) {
            if ($e_wallet_account->last_limit_reset != $todayDate) {
                $e_wallet_account->daily_received = 0;
                $e_wallet_account->daily_sent = 0;
                $e_wallet_account->last_limit_reset = $todayDate;
                $e_wallet_account->save();
            }
            if (date('m', strtotime($e_wallet_account->last_limit_reset)) != $thisMonth) {
                $e_wallet_account->monthly_received = 0;
                $e_wallet_account->monthly_sent = 0;
                $e_wallet_account->last_limit_reset = $todayDate;
                $e_wallet_account->save();
            }

        }
    }

    public function updateEWallets()
    {
        $records = EWalletAccount::select('e_wallet_name','account_no','is_live')->get();
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

    public function newFundOpen(Request $request, $gate, $charge, $final_amo, $amount, $account_no, $open_user, $e_wallet_phone_number): Fund
    {

        $fund = new Payment();
        $fund->user_id = 0;
        $fund->gateway_id = $gate->id;
        $fund->amount = $amount;
        $fund->charge = $charge;
        $fund->sender = $account_no;
        $fund->transaction = strRandom();
        $fund->try = 0;
        $fund->status = 2;
        $fund->api_id = $open_user->id;
        $fund->e_wallet_phone_number = $e_wallet_phone_number;
        $fund->request_source = "URL";
        $fund->save();
        return $fund;
    }



    public function apis(Request $request)
    {

        $user = Auth::guard('partner')->user();
        $records = PartnerCommission::with('api')
        ->select('api_id',
            \DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) AS sum_amount_type_1'),
            \DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) AS sum_charges_type_1'),
            \DB::raw('SUM(CASE WHEN type = 1 THEN total_amount ELSE 0 END) AS sum_total_amount_type_1'),
            \DB::raw('SUM(CASE WHEN type = 1 THEN profit ELSE 0 END) AS sum_profit_type_1'),
            \DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) AS sum_amount_type_2'),
            \DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) AS sum_charges_type_2'),
            \DB::raw('SUM(CASE WHEN type = 2 THEN total_amount ELSE 0 END) AS sum_total_amount_type_2'),
            \DB::raw('SUM(CASE WHEN type = 2 THEN profit ELSE 0 END) AS sum_profit_type_2')
        )
        ->where('from_id', $user->id)
        ->where('status', 1)
        ->groupBy('api_id')
        ->orderBy('api_id', 'DESC')
        ->get();
        $pageTitle = "Commissions Summary";
        $partners = Api::where('type', 'Admin')->get();
        return view('partner.payout.api', compact('records', 'pageTitle', 'partners'));
    }


    public function apiCommissions(Request $request)
    {
        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
        $partnerTimezone = $main_admin->timezone;
        $partner_ids = PartnerCommission::where('from_id', $user->id)
            ->distinct()
            ->pluck('api_id')
            ->toArray();


        // If no partner IDs are found, set an empty collection
        if (empty($partner_ids)) {
            $partners = collect();
        } else {
            $partners = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                ->get();
        }

        // Create a query builder for PartnerCommission
        $records = PartnerCommission::query();

        // Check if from_date and to_date are provided, otherwise set today's date
        $from_date = !empty($request->from_date) ? $request->from_date : Carbon::today()->toDateString();
        $to_date = !empty($request->to_date) ? $request->to_date : Carbon::today()->toDateString();


        $from_date_to_search = date('Y-m-d H:i:s', strtotime($from_date . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($to_date . ' 23:59:59'));


        $partnerTimezone = $main_admin->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);


        // Apply date filters
        $records->where('created_at', '>=', $from_date_to_search);
        $records->where('created_at', '<=', $to_date_to_search);

        // Filter by partner if provided
        if (!empty($request->partner)) {
            $records->where('api_id', $request->partner);
        }

        // Filter by from_id (the current user's id)
        $records->where('from_id', $user->id);

        // Filter by type if provided
        if (!empty($request->type) || $request->type == '0') {
            $records->where('type', $request->type);
        }

        // Ensure the status is 1 (active)
        $records->where('status', 1);

        // Paginate the results
        $records = $records->orderBy('id', 'DESC')->paginate(config('basic.paginate'));

        // Set the page title for the view
        $pageTitle = "Commission History";

        // Return the view with data
        return view('partner.payout.commission_report', compact('records', 'pageTitle', 'partners' , 'from_date' , 'to_date'));
    }


    public function settlements()
{
    $user = Auth::guard('partner')->user();

    if ($user->type !== "Admin") {
        return back()->with('error', 'You have no permission to this page.');
    }

    $now = now();

    // Calculate total settled amount for current month
    $monthlyTotal = Settlement::whereYear('created_at', $now->year)
        ->whereMonth('created_at', $now->month)
        ->where('partner_id', $user->id)
        ->where('status', 1)
        ->sum('amount');

    // Find admin API
    $api = Api::where([
        ['api_key', $user->api_key],
        ['type', 'Admin']
    ])->first();

    $settlementableAmount = $user->balance;
    $charge = 0;

    if ($api) {
        $commission = Commission::where('api_id', $api->id)
            ->where('from_amount', '<=', $monthlyTotal)
            ->where('to_amount', '>=', $monthlyTotal)
            ->first();

        if (!$commission) {
            $commission = Commission::where('api_id', $api->id)
                ->orderByDesc('to_amount')
                ->first();
        }

        if ($commission) {
            $charge = ($commission->settlement_percentage / 100) * $user->balance;
            $settlementableAmount -= $charge;
        }
    }

    // All settlements
    $records = Settlement::where('partner_id', $user->id)
        ->latest()
        ->get();

    // Gateway summary
    $gateways = Settlement::where('partner_id', $user->id)
        ->select('source_name', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(amount) as total'))
        ->groupBy('source_name')
        ->get();

    return view('partner.payout.settlement', [
        'records' => $records,
        'gateways' => $gateways,
        'settlementable_amount' => $settlementableAmount,
        'pageTitle' => 'Settlements History'
    ]);
}


public function settlementSearch(Request $request)
{
    $user = Auth::guard('partner')->user();

    if ($user->type !== "Admin") {
        return back()->with('error', 'You have no permission to this page.');
    }

    $now = now();

    // Monthly settled amount
    $monthlyTotal = Settlement::whereYear('created_at', $now->year)
        ->whereMonth('created_at', $now->month)
        ->where('partner_id', $user->id)
        ->where('status', 1)
        ->sum('amount');

    // Get API and timezone
    $api = Api::where('api_key', $user->api_key)
        ->where('type', 'Admin')
        ->first();

    $charge = 0;
    if ($api) {
        $commission = Commission::where('api_id', $api->id)
            ->where('from_amount', '<=', $monthlyTotal)
            ->where('to_amount', '>=', $monthlyTotal)
            ->first();

        if (!$commission) {
            $commission = Commission::where('api_id', $api->id)
                ->orderByDesc('to_amount')
                ->first();
        }

        if ($commission) {
            $charge = ($commission->settlement_percentage / 100) * $user->balance;
        }
    }

    $settlementable_amount = $user->balance - $charge;

    // Build query
    $recordsQuery = Settlement::where('partner_id', $user->id);

    // Handle date conversion
    $originalTimezone = $api?->timezone ?? config('app.timezone');
    $targetTimezone = config('app.timezone', 'Asia/Dhaka');

    if (!empty($request->from_date)) {
        $from = Carbon::parse($request->from_date . ' 00:00:00', $originalTimezone)
            ->setTimezone($targetTimezone);
        $recordsQuery->where('created_at', '>=', $from);
    }

    if (!empty($request->to_date)) {
        $to = Carbon::parse($request->to_date . ' 23:59:59', $originalTimezone)
            ->setTimezone($targetTimezone);
        $recordsQuery->where('created_at', '<=', $to);
    }

    if (!empty($request->gateway)) {
        $recordsQuery->where('source_name', $request->gateway);
    }

    if ($request->status !== "all") {
        if ($request->status === "0" || !empty($request->status)) {
            $recordsQuery->where('status', $request->status);
        }
    }

    $records = $recordsQuery->latest()->get();

    // Only get unique gateways with aggregation to avoid groupBy issue
    $gateways = Settlement::where('partner_id', $user->id)
        ->select('source_name', \DB::raw('COUNT(*) as total'))
        ->groupBy('source_name')
        ->get();

    $pageTitle = "Search Settlements History";

    return view('partner.payout.settlement', compact(
        'records',
        'pageTitle',
        'gateways',
        'settlementable_amount'
    ));
}

public function reportDetail($date, $gateway, $status)
    {
        $log = "View Day Wise Withdrawal Report Detail";
        $this->addLogs($log);


        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $partnerTimezone = $main_user->timezone;
        $api_id = $main_user->id;

        $gateways = PayoutMethod::where('status', 1)
            ->get();
        // dd($gateways);
        $page_title = "Payout Report Detail";
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

        $from_date_to_search = date('Y-m-d H:i:s', strtotime($date . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($date . ' 23:59:59'));


        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $records = PayoutLog::where('status', 'like', '%' . $status . '%')
            ->where('status', '!=', 0)
            ->orderBy('id', 'DESC')
            ->with('user', 'method', 'payout')
            ->when($api_id, function ($query) use ($api_id) {
                $query->where(function ($subQuery) use ($api_id) {
                    $subQuery->whereHas('payout', function ($subQuery) use ($api_id) {
                        $subQuery->where('api_id', $api_id);
                    })->orWhere('api_id', $api_id);
                });
            })
            ->whereHas('payout', function ($query) use ($date, $gateway,$from_date_to_search,$to_date_to_search) {
                $query->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
                    ->where('e_wallet_name', 'like', '%' . $gateway . '%'); // Add the e_wallet_name condition
            })
            ->get()->map(function ($fund) use ($partnerTimezone) {
            $fund->created_at = \Carbon\Carbon::parse($fund->created_at)->timezone($partnerTimezone);
            $fund->updated_at = \Carbon\Carbon::parse($fund->updated_at)->timezone($partnerTimezone);
            return $fund;
        });



        $funds_t = PayoutLog::where('status', '!=', 0)->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
            ->where('status', 'like', '%' . $status . '%')
            ->with('user', 'method', 'payout')
            ->when($api_id, function ($query) use ($api_id) {
                $query->where(function ($subQuery) use ($api_id) {
                    $subQuery->whereHas('payout', function ($subQuery) use ($api_id) {
                        $subQuery->where('api_id', $api_id);
                    })->orWhere('api_id', $api_id);
                });
            })
            ->whereHas('payout', function ($query) use ($date, $gateway,$from_date_to_search,$to_date_to_search) {
                $query->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search) // Add the date condition
                    ->where('e_wallet_name', 'like', '%' . $gateway . '%'); // Add the e_wallet_name condition
            })->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);

        return response()->json($records);

        // dd($records);

        // return view('partner.payout.report_detail', compact('records', 'page_title','domains','gateways','fund_count','fund_sum','heading'));
    }


    public function dailyReportSettlement()
    {
        $this->addLogs("View Day Wise Settlement Report");

        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
        $website = $user->website;

        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');

        $from_datetime = Carbon::parse($from_date . ' 00:00:00', $main_admin->timezone ?? 'UTC')
            ->setTimezone(config('app.timezone', 'Asia/Dhaka'));
        $to_datetime = Carbon::parse($to_date . ' 23:59:59', $main_admin->timezone ?? 'UTC')
            ->setTimezone(config('app.timezone', 'Asia/Dhaka'));

        $offset = Carbon::now(new CarbonTimeZone($main_admin->timezone))->format('P');

        $gateways = Settlement::where('partner_id', $user->id)
            ->select('source_name', DB::raw('COUNT(*) as total'))
            ->groupBy('source_name')
            ->get();

        $settlementsByDate = Settlement::select(
                DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as settlement_date"),
                DB::raw('COUNT(*) as settlement_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(CASE WHEN status = 0 THEN 1 END) as pending_count'),
                DB::raw('COUNT(CASE WHEN status = 1 THEN 1 END) as complete_count'),
                DB::raw('SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as complete_amount')
            )
            ->whereBetween('created_at', [$from_datetime, $to_datetime])
            ->where('partner_id', $user->id)
            ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"))
            ->get();

        $pageTitle = "Daily Settlement Report";

        return view('partner.payout.settlement_report', compact(
            'settlementsByDate',
            'pageTitle',
            'gateways',
            'from_date',
            'to_date'
        ));
    }


    public function partnerBalance(Request $request)
    {
            $user = Auth::guard('partner')->user();
            $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
            $api_id = $main_admin->id;

        $records = ApiTransaction::where('partner_id', $api_id)->with('api')->orderBy('id', 'DESC')->get();
        $pageTitle = "Adjustments";
        $partners = Api::where('type', 'Admin')->get();

        return view('partner.payout.partner_balance', compact('records', 'pageTitle', 'partners'));
    }


    public function partnerBalanceSearch(Request $request)
    {

        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
        $partnerTimezone = $main_admin->timezone;
        $api_id = $main_admin->id;


        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');

        if(!empty($request->from_date)){
            $from_date_to_search = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
            $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        }

        if(!empty($request->to_date)){
            $to_date_to_search = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));
            $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        }


        $partners = Api::where('type', 'Admin')->get();

        $records = ApiTransaction::with('api');

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $records->whereDate('created_at', '>=', $from_date_to_search);
            $records->whereDate('created_at', '<=', $to_date_to_search);
        } elseif (!empty($request->from_date)) {
            $records->whereDate('created_at', '>=', $from_date_to_search);
        } elseif (!empty($request->to_date)) {
            $records->whereDate('created_at', '<=', $to_date_to_search);
        }

        $records->where('partner_id', $api_id);

        if (!empty($request->adjustment) || $request->adjustment == '0') {
            $records->where('adjustment', $request->adjustment);
        }

        $records = $records->orderBy('id', 'DESC')->get();

        $pageTitle = "Search Adjustments";
        return view('partner.payout.partner_balance', compact('records', 'pageTitle', 'partners'));
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



}
