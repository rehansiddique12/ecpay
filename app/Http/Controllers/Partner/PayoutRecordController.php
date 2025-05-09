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
use App\Models\PayoutLog;
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
                $order = Fund::where('id', $fund['id'])->first();
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
                    $payment->created_at = $order->created_at;
                    $payment->trans_complete_date = Carbon::now();
                    $payment->completed_source = 'PartnerLink';
                    $payment->save();

                    $order->status = 1;
                    $order->created_at = $order->created_at;
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

    public function processTransection($username, $ewallet, $acc, $amount, $transection_id = 0, $sign = null, $member_id = null)
    {
        if (session()->has('txn_verified')) {
            session()->forget('txn_verified');
        }

        $remainingTime = 0;

        $data = [];
        $message = "";
        $ewalletee = strtolower($ewallet);
        if ($ewalletee == 'bkash') {
            $logo = asset('assets/images/ifram_bkash_logo.png');
            $banner = asset('assets/images/bKash_Background.jpg');
        }
        if ($ewalletee == 'nagad') {
            $logo = asset('assets/images/iframe_nagad_logo.png');
            $banner = asset('assets/images/Nsagad_backgroudn.jpg');
        }
        if ($ewalletee == 'rocket') {
            $logo = asset('assets/images/iframe_rocket_logo.png');
            $banner = asset('assets/images/Rocket_Background.jpg');
        }

        $txn_verification = "";

        $api_key = API::where('username', $username)->first();
        if ($api_key && $api_key->type == "Admin") {
            $source = $api_key->website;
            $api_id = $api_key->id;
            $secretKey = $api_key->secret_key;
            $txn_verification = $api_key->txn_verification;

            if ($acc == 0) {
                $acc = "";
            }

            if ($txn_verification == 0) {

                if (!is_numeric($acc)) {
                    return response()->json(['code' => 605, 'error' => 'Account number formate not valid'], 404);
                }

                if (substr($acc, 0, 2) === "01") {
                    $num_digits = strlen($acc);
                    if ($ewalletee == 'bkash' && $num_digits != 11) {
                        return response()->json(['code' => 605, 'message' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'nagad' && $num_digits != 11) {
                        return response()->json(['code' => 605, 'message' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'rocket' && ($num_digits < 11 || $num_digits > 12)) {
                        return response()->json(['code' => 605, 'message' => 'Account number should be 11 or 12 digit'], 404);
                    }
                } else {
                    return response()->json(['code' => 605, 'message' => 'Account number should start from 01'], 404);
                }
            }

            if ($api_key->sign == 1) {
                if (isset($sign) && !empty($sign)) {
                    if ($api_key->txn_verification == 0) {
                        $string_to_hash = json_encode(array(
                            "amount" => $amount,
                            "api_key" => $api_key->api_key,
                            "e_wallet_name" => $ewallet,
                            "user_account_no" => $acc
                        ));
                    } else {
                        $string_to_hash = json_encode(array(
                            "amount" => $amount,
                            "api_key" => $api_key->api_key,
                            "e_wallet_name" => $ewallet
                        ));
                    }


                    // return $string_to_hash;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);

                    $timestamp = time();
                    $timestamp_str = (string) $timestamp;
                    $timestamp_length = strlen($timestamp_str);
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
            $message = "Wrong Username OR Username Not Exist";
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }

        $amount = str_replace(',', '', $amount);
        $gate = Gateway::where('code', $ewallet)->where('status', 1)->first();
        if(!$gate){
            $message = "Wrong E-Wallet Name!";
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }

        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount,2);
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }




        $now = Carbon::now();
        $twoHoursAgo = $now->subHours(2);
        if (!empty($transection_id) || $transection_id != "0") {
            $fund = Payment::where('partner_transection_id', $transection_id)->where('api_id', $api_key->id)->latest()->first();
            if($fund){
                if($fund->status != 2){
                    $message = "Your Transection Already Processed!";
                    return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
                }
            }
        } else {
            $fund = Payment::where('gateway_id', $gate->id)->where('amount', $amount)->where('status', 2)->where('account_no', $acc)->where('api_id', $api_key->id)->where('source', 'Iframe')->where('created_at', '>=', $twoHoursAgo)->latest()->first();
        }

        if (!$fund) {

                        $this->updateLimits();
            $this->updateEWallets();

            $current_time = Carbon::now('Asia/Dhaka');

                $Setting = Setting::where('name', 'last_account_active')->first();

                $recordcounts = Payment::where('gateway_id', $gate->id)
                    ->where('created_at', '>=', $Setting->value)
                    ->select('e_wallet_phone_number', DB::raw('count(*) as total'))
                    ->groupBy('e_wallet_phone_number')
                    ->pluck('total', 'e_wallet_phone_number')
                    ->toArray();

                $account = EWalletAccount::where('e_wallet_name', $ewallet)
                    ->where('monthly_limit', '>', 'monthly_received')
                    ->whereRaw('daily_limit - daily_received > ?', [$amount])
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

                    // dd($recordcounts);

            if (!$account) {
                $message = "You Can not Proceed With this E-wallet account";
                return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
            }


            $currentMonth = now()->format('Y-m');
            $sum = Payment::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('api_id', $api_id)
                ->where('status', 'Complete')
                ->sum('amount');

            $charge = 0;
            $commissions = Commission::where('api_id', $api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            } else {
                $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
                if ($commissions) {
                    $charge = $commissions->deposit_percentage * $amount / 100;
                }
            }


            $reqAmount = $amount;
            $payable = getAmount($reqAmount - $charge);
            $final_amo = getAmount($payable * $gate->convention_rate);
            $e_wallet_phone_number = $account->account_no;


            $fund = new Payment();
            $fund->user_id = 0;
            $fund->gateway_id = $gate->id;
            $fund->amount = $amount;
            $fund->partner_transection_id = $transection_id;
            if (isset($member_id) && !empty($member_id)) {
                $fund->member_id = $member_id;
            }

            $fund->charge = $charge;
            $fund->e_wallet_name = $gate->name;
            $fund->sender = $acc;
            $fund->transaction = strRandom();
            $fund->try = 0;
            $fund->status = 'Complete';
            $fund->api_id = $api_key->id;
            $fund->e_wallet_phone_number = $e_wallet_phone_number;
            $fund->request_source = "Iframe";
            $fund->save();

            if ($charge > 0 && $api_key->parent_id > 0) {
                // $parent_commissions = Commission::where('id', $commissions->parent_id)->first();
                if ($commissions->parent_id > 0 && $commissions->parent_deposit_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $api_key->parent_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage;
                    $profit_p = $commissions->parent_deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();

                    //  $main_parent_commissions = Commission::where('id', $parent_commissions->parent_id)->first();

                }

                if ($commissions->parent2_id > 0 && $commissions->parent2_deposit_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $commissions->parent2_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage;
                    $profit_p = $commissions->parent2_deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }
        }else{

            if($fund->gateway_id != $gate->id || $fund->amount != $amount){
                $message = "You have already created a transaction before this Transaction ID!";
                return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
            }

            $account = EWalletAccount::where('e_wallet_name', $ewallet)
                ->where('account_no', $fund->e_wallet_phone_number)
                ->whereIn('account_type', ['Deposit', 'Both'])
                ->orderBy('daily_received', 'asc')
                ->first();

            if (!$account) {
                $message = "You Can not Proceed With this E-wallet account";
                return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
            }
        }

        // env('APP_URL') . config('location.gateway.path')

        $now = Carbon::now();
        $tenMinutesAgo = $now->subMinutes(10);

        if ($fund->created_at > $tenMinutesAgo) {
            $remainingTime = $fund->created_at->diffInSeconds($now);
        } else {
            $fund->created_at = Carbon::now();
            $fund->updated_at = Carbon::now();
            $fund->save();
            $remainingTime = 600;
        }

        $data['id'] = $fund->id;
        $data['name'] = $account->e_wallet_name;
        $data['amount'] = $amount;
        $data['phone_number'] = $account->account_no;
        $data['account_type'] = $account->type;
        $data['qr_image'] = $account->image ? (env('APP_URL') . config('location.withdraw.path') . $account->image) : '';
        $data['gateway_image'] = $gate->image ? (env('APP_URL') . config('location.gateway.path') . $gate->image) : '';

        // setting for theme style
        return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
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


    public function processNextPayment(Request $request, $id)
    {
        $maxAttempts = 3;
        $attempt = 0;
        $success = 0;
        $txn_id = "";
        if ($request->filled('txn')) {
            $txn_id = $request->txn;
        }

        while ($attempt < $maxAttempts && $success==0) {

            LaravelLog::info('processNextPayment-PartnerController try('. $attempt + 1 .') txn_id: '.$txn_id);

            DB::beginTransaction();
            try {
                $message = "";
                $processing = 1;
                $remainingTime = 0;
                $url = "";
                // dd($id);
                $order = Payment::where('id', $id)->with(['gateway', 'user'])->whereIn('status', [0, 2])->lockForUpdate()->first();
                if (!$order) {
                    dd('order not Found');
                    DB::rollBack();
                    abort(404);
                }
                $ewallet = strtolower($order->gateway->code);

                if ($ewallet == 'bkash') {
                    $logo = asset('assets/images/ifram_bkash_logo.png');
                    $banner = asset('assets/images/bKash_Background.jpg');
                }
                if ($ewallet == 'nagad') {
                    $logo = asset('assets/images/iframe_nagad_logo.png');
                    $banner = asset('assets/images/Nsagad_backgroudn.jpg');
                }
                if ($ewallet == 'rocket') {
                    $logo = asset('assets/images/iframe_rocket_logo.png');
                    $banner = asset('assets/images/Rocket_Background.jpg');
                }

                $fiveMinutesAgo = Carbon::now()->subMinutes(5)->timestamp;
                if (isset($request->time) && $request->time > $fiveMinutesAgo) {
                    $remainingTime = $request->time - $fiveMinutesAgo;
                } else {
                    $processing = 2;
                    $message = "Timeout.";
                    return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url'));
                }

                $open_user = API::where('id', $order->api_id)->lockForUpdate()->first();
                if (!$open_user || $open_user->type != "Admin") {
                    DB::rollBack();
                    abort(404);
                }

                $url = $open_user->redirect_url;

                if ($open_user->txn_verification == 1) {
                    if (!$request->filled('txn') || empty($request->txn)) {
                        DB::rollBack();
                        return back()->with('error', 'Kindly Fill Transaction Number.');
                    }

                    $api_key = $open_user;
                    if ($api_key) {
                        $source = $api_key->website;
                        $api_id = $api_key->id;
                        if (empty($source)) {
                            $source = "";
                        }

                        $secretKey = $api_key->secret_key;
                    } else {
                        DB::rollBack();
                        return back()->with('error', 'Wrong API key.');
                    }
                    //
                    $currentMonth = now()->format('Y-m');
                    $now = Carbon::now();
                    $twoHoursAgo = $now->subHours(2);

                    $Txn = Txn::where('txn_no', $request->txn)->where('api_id', $api_id)->orderBy('id', 'DESC')->first();
                    if (!$Txn) {
                        $Txn = new Txn();
                        $Txn->txn_no = $request->txn;
                        $Txn->partner_transection_id = $order->partner_transection_id;
                        $Txn->api_id = $api_id;
                        $Txn->save();
                    }

                    $payment_record = Payment::where('txn_id', $request->txn)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    if (!$payment_record) {
                        $processing = 1;
                        $message = "Please Wait! Your Payment is Processing.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url'));
                    }

                    if ($payment_record->status == "Complete") {
                        $processing = 2;
                        $message = "With This Transaction No. Payment Already Completed.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url'));
                    }

                    $charge = 0;
                    $commit = 0;
                    if ($order && $order->amount == $payment_record->amount) {
                        if ($order->status == 1) {
                            $processing = 2;
                            $message = "Your Payment is Already Verified!";
                            DB::rollBack();
                            return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url'));
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

                            $net_amount = $payment_record->amount - $charge;
                            $partner_api_key->balance += $net_amount;
                            $partner_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $payment_record->updated_at;
                            $Log->final_amount = $net_amount;
                            $Log->balance = $partner_api_key->balance;
                            $Log->transection_type = 1;
                            $Log->transection_id = $payment_record->id;
                            $Log->partner_id = $partner_api_key->id;
                            $Log->source = 'Iframe';
                            $Log->save();
                        }

                        if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                            if(!empty($order->account_no)){
                                $payment_record->sender = $order->account_no;
                            }
                        }

                        $payment_record->status = 'Complete';
                        $order->status = 1;
                        $order->created_at = $order->created_at;
                        $order->trans_completed_date = Carbon::now();
                        $payment_record->created_at = $order->created_at;
                        $payment_record->trans_complete_date = Carbon::now();
                        $payment_record->completed_source = 'Iframe';

                        $payment_record->transaction_id = $order->id;
                        $payment_record->api_id = $api_id;
                        $payment_record->source = $source;
                        $payment_record->charge = $charge;
                        $payment_record->partner_transection_id = $order->partner_transection_id;
                        $payment_record->member_id = $order->member_id;
                        $payment_record->save();
                        $order->account_no = $payment_record->sender;
                        $order->payment_id = $payment_record->id;
                        $order->save();

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
                            $summary_log->payment_id = $payment_record->id;
                            $summary_log->total_amount = $net_amount;
                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                            $summary_log->source = 'Iframe';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
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
                            $Log->source = 'Iframe';
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
                                $summary_log->source = 'Iframe';
                                $summary_log->save();
                            }
                        }

                        DB::commit();
                        $commit = 1;

                        if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($payment_record->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $payment_record->e_wallet_name,
                                "id" => strval($payment_record->id),
                                'transaction_type' => 'Deposit',
                                "user_account_no" => strval($payment_record->sender),

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
                                        'user_account_no' => $payment_record->sender,
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

                    $processing = 1;
                    $message = "Please Wait! Your Payment is Processing.";
                    if($commit==0){
                        DB::commit();
                    }

                    return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url'));
                }
                DB::commit();
                return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url'));
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                }else{
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('processNextPayment-PartnerController Error: txn_id: '.$txn_id. ' Error: ' .$e->getMessage());


            }
        }


        return back()->with('error', $e->getMessage());

    }

    public function processNextPayment3(Request $request)
    {
        $message = "";
        $processing = 1;
        $remainingTime = 0;
        $url = "";
        $id = $request->id;
        $ewallet = $request->ewallet;
        $txn_id = $request->txn;

        $fund = Payment::where('id', $request->id)->latest()->first();
        $fiveMinutesAgo = Carbon::now()->subMinutes(5)->timestamp;
        if (isset($request->time) && $request->time > $fiveMinutesAgo) {
            $remainingTime = $request->time - $fiveMinutesAgo;
        }


        $Txn = Txn::where('txn_no', $request->txn)->where('api_id', $fund->api_id)->where('partner_transection_id', $fund->partner_transection_id)->orderBy('id', 'DESC')->first();
        if (!$Txn) {
            $Txn = new Txn();
            $Txn->txn_no = $request->txn;
            $Txn->partner_transection_id = $fund->partner_transection_id;
            $Txn->api_id = $fund->api_id;
            $Txn->save();
        }

        $fund->try = $fund->try + 1;
        $fund->save();

        $order = $fund;

        $ewallet = strtolower($ewallet);
        if (strtolower($ewallet) == 'bkash') {
            $logo = asset('assets/images/ifram_bkash_logo.png');
            $banner = asset('assets/images/bKash_Background.jpg');
        }
        if (strtolower($ewallet) == 'nagad') {
            $logo = asset('assets/images/iframe_nagad_logo.png');
            $banner = asset('assets/images/Nsagad_backgroudn.jpg');
        }
        if (strtolower($ewallet) == 'rocket') {
            $logo = asset('assets/images/iframe_rocket_logo.png');
            $banner = asset('assets/images/Rocket_Background.jpg');
        }



        $now = Carbon::now();
        $twoHoursAgo = $now->subHours(2);

        DB::beginTransaction();
        $payment_record = Payment::where('txn_id', $request->txn)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
        // dd($request->all());
        if (!$payment_record) {
            $processing = 1;
            $message = "Please Wait! Your Payment is Processing.";
            DB::commit();
            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
        }


        if ($payment_record->status == "Complete") {
            $processing = 2;
            $message = "With This Transaction No. Payment Already Completed.";
            DB::commit();
            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
        }




        $order = Payment::where('id', $fund->id)->whereIn('status', [0, 2])->lockForUpdate()->first();
        if (!$order) {
            DB::rollBack();
            abort(404);
        }




        $open_user = API::where('id', $order->api_id)->lockForUpdate()->first();
        if (!$open_user || $open_user->type != "Admin") {
            DB::rollBack();
            abort(404);
        }

        $url = $open_user->redirect_url;

        $source = $open_user->website;
        $api_id = $open_user->id;
        if (empty($source)) {
            $source = "";
        }

        $commit = 0;
        if ($order && $order->amount == $payment_record->amount) {
            if ($order->status == 1) {
                $processing = 2;
                $message = "Your Payment is Already Verified!";
                DB::rollBack();
                return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
            }
            $partner_api_key = $open_user;
            if ($source != env('APP_WEBSITE')) {


                $charge = $order->charge;

                $net_amount = $payment_record->amount - $charge;
                $partner_api_key->balance += $net_amount;
                $partner_api_key->save();

                $Log = new Log();
                $Log->date_time = $payment_record->updated_at;
                $Log->final_amount = $net_amount;
                $Log->balance = $partner_api_key->balance;
                $Log->transection_type = 1;
                $Log->transection_id = $payment_record->id;
                $Log->partner_id = $partner_api_key->id;
                $Log->source = 'Iframe';
                $Log->save();
            }

            if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                if(!empty($order->account_no)){
                    $payment_record->sender = $order->account_no;
                }
            }

            $payment_record->status = 'Complete';
            $order->status = 1;
            $order->created_at = $order->created_at;
            // $order->trans_completed_date = Carbon::now();
            $payment_record->created_at = $order->created_at;
            // $payment_record->trans_complete_date = Carbon::now();
            $payment_record->completed_source = 'Iframe';

            $payment_record->api_id = $api_id;
            $payment_record->request_source  = $source;
            $payment_record->charge = $charge;
            $payment_record->partner_transection_id = $order->partner_transection_id;
            $payment_record->member_id = $order->member_id;
            $payment_record->save();
            $order->sender = $payment_record->sender;
            $order->save();

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
                $summary_log->payment_id = $payment_record->id;
                $summary_log->total_amount = $net_amount;
                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                $summary_log->source = 'Iframe';
                $summary_log->save();
            }

            $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
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
                $Log->source = 'Iframe';
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
                    $summary_log->source = 'Iframe';
                    $summary_log->save();
                }
            }

                $commit = 1;
                DB::commit();


            if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                $string_to_hash = json_encode(array(
                    "amount" => strval($this->convertStringToNumber($payment_record->amount)),
                    "api_key" => $partner_api_key->api_key,
                    "e_wallet_name" => $payment_record->e_wallet_name,
                    "id" => strval($payment_record->id),
                    'transaction_type' => 'Deposit',
                    "user_account_no" => strval($payment_record->sender),

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
                            'user_account_no' => $payment_record->sender,
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

        $processing = 1;
        $message = "Please Wait! Your Payment is Processing.";
        if($commit==0){
            DB::commit();
        }
        return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
    }

    public function update_order_fund_status_iframe($id)
    {
        $payment = Payment::where('id', $id)->where('status', 'Complete')->orderBy('id', 'DESC')->first();
        if ($payment) {
            return json_encode(['status' => 'success']);
        } else {
            return json_encode(['status' => 'false']);
        }
    }

    public function processTransection2($username, $ewallet, $acc, $amount, $transection_id = 0, $sign = null, $member_id = null)
    {
        $remainingTime = 600;
        $amount = str_replace(',', '', $amount);
        $data = [
            'username' => $username,
            'ewallet' => $ewallet,
            'acc' => $acc,
            'amount' => $amount,
            'transection_id' => $transection_id,
            'member_id' => $member_id,
            'phone_number' => ''
        ];

        $data_jsaon =  json_encode($data);
        LaravelLog::info('processTransection2:'.$data_jsaon);

        $message = "";
        $banner = "";
        $txn_verification = "";
        $ewalletee = strtolower($ewallet);
        if ($ewalletee == 'bkash') {
            $logo = asset('assets/images/ifram2_bkash_logo.png');
            $ewallet_to_show = "bKash";

        }
        if ($ewalletee == 'nagad') {
            $logo = asset('assets/images/ifrmrame_Nagad_Logo.png');
            $ewallet_to_show = "Nagad";
        }
        if ($ewalletee == 'rocket') {
            $logo = asset('assets/images/iframe_rocket_logo.png');
            $ewallet_to_show = "Rocket";
        }

        $api_key = API::where('username', $username)->select('id','type','secret_key','txn_verification','redirect_url','sign','api_key','min_deposit','parent_id')->first();
        if ($api_key && $api_key->type == "Admin") {
            $api_id = $api_key->id;
            $secretKey = $api_key->secret_key;
            $txn_verification = $api_key->txn_verification;
            $data['redirect_url'] = $api_key->redirect_url;
            if ($acc == 0) {
                $acc = "";
            }
            if ($txn_verification == 0) {

                if (!is_numeric($acc)) {
                    return response()->json(['code' => 605, 'error' => 'Account number formate not valid'], 404);
                }

                if (substr($acc, 0, 2) === "01") {
                    $num_digits = strlen($acc);
                    if ($ewalletee == 'bkash' && $num_digits != 11) {
                        return response()->json(['code' => 605, 'message' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'nagad' && $num_digits != 11) {
                        return response()->json(['code' => 605, 'message' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'rocket' && ($num_digits < 11 || $num_digits > 12)) {
                        return response()->json(['code' => 605, 'message' => 'Account number should be 11 or 12 digit'], 404);
                    }
                } else {
                    return response()->json(['code' => 605, 'message' => 'Account number should start from 01'], 404);
                }
            }

            if ($api_key->sign == 1) {
                if (isset($sign) && !empty($sign)) {
                    if ($api_key->txn_verification == 0) {
                        $string_to_hash = json_encode(array(
                            "amount" => $amount,
                            "api_key" => $api_key->api_key,
                            "e_wallet_name" => $ewallet,
                            "user_account_no" => $acc
                        ));
                    } else {
                        $string_to_hash = json_encode(array(
                            "amount" => $amount,
                            "api_key" => $api_key->api_key,
                            "e_wallet_name" => $ewallet
                        ));
                    }


                    // return $string_to_hash;
                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);

                    $timestamp = time();
                    $timestamp_str = (string) $timestamp;
                    $timestamp_length = strlen($timestamp_str);
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
            $message = "Wrong Username OR Username Not Exist";
            return view('partner.payout.process_transection2', compact('ewallet_to_show','data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        $gate = Gateway::where('code', $ewallet)->where('status', 1)->first();

        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return view('partner.payout.process_transection2', compact('ewallet_to_show','data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount,2);
            return view('partner.payout.process_transection2', compact('ewallet_to_show','data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        $data['gate_id'] = $gate->id;
        $data['phone_number'] = "Loading...";
        $data['account_type'] = "";

        // setting for theme style
        return view('partner.payout.process_transection2', compact('ewallet_to_show','data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
    }

    public function processNextPayment2(Request $request)
    {

        $username = $request->username;
        $ewallet = $request->ewallet;
        $amount = $request->amount;
        $fund_id = $request->fund_id;



        $api_key = API::where('username', $username)->where('type', 'Admin')->first();
        if ($api_key) {
            $secretKey = $api_key->secret_key;
        } else {
            return back()->with('error', 'Wrong API key.');
        }
        $api_id = $api_key->id;

        $currentMonth = now()->format('Y-m');
        $sum = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');

        $charge = 0;
        $commissions = Commission::where('api_id', $api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            }
        }


        $fund = Payment::where('id', $fund_id)->latest()->first();
        if ($fund) {
            if ($charge > 0 && $api_key->parent_id > 0) {
                if ($commissions->parent_id > 0 && $commissions->parent_deposit_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $api_key->parent_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage;
                    $profit_p = $commissions->parent_deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund->id;
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();

                }

                if ($commissions->parent2_id > 0 && $commissions->parent2_deposit_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $commissions->parent2_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage;
                    $profit_p = $commissions->parent2_deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund->id;
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }
        }else{
            $message = "Your Transection Already Processed!";
            return back()->with('error', $message);
        }

        $maxAttempts = 3;
        $attempt = 0;
        $success = 0;

        $txn_id = "";
        if ($request->filled('txn')) {
            $txn_id = $request->txn;
        }





        while ($attempt < $maxAttempts && $success==0) {
            LaravelLog::info('processNextPayment-PartnerController try('. $attempt + 1 .') txn_id: '.$txn_id);
            try {
                $message = "";
                $processing = 1;
                $remainingTime = 0;
                $url = "";

                $order = $fund;
                $id = $fund->id;

                $ewallet = strtolower($ewallet);
                if (strtolower($ewallet) == 'bkash') {
                    $logo = asset('assets/images/ifram_bkash_logo.png');
                    $banner = asset('assets/images/bKash_Background.jpg');
                }
                if (strtolower($ewallet) == 'nagad') {
                    $logo = asset('assets/images/iframe_nagad_logo.png');
                    $banner = asset('assets/images/Nsagad_backgroudn.jpg');
                }
                if (strtolower($ewallet) == 'rocket') {
                    $logo = asset('assets/images/iframe_rocket_logo.png');
                    $banner = asset('assets/images/Rocket_Background.jpg');
                }

                $url = $api_key->redirect_url;


                $fiveMinutesAgo = Carbon::now()->subMinutes(5)->timestamp;
                if (isset($request->time) && $request->time > $fiveMinutesAgo) {
                    $remainingTime = $request->time - $fiveMinutesAgo;
                } else {
                    // $processing = 2;
                    // $message = "Timeout.";
                    // return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
                }



                if ($api_key->txn_verification == 1) {
                    if (!$request->filled('txn') || empty($request->txn)) {
                        return back()->with('error', 'Kindly Fill Transaction Number.');
                    }
                     $source = $api_key->website;
                    $api_id = $api_key->id;
                    if (empty($source)) {
                        $source = "";
                    }


                    //
                    $currentMonth = now()->format('Y-m');
                    $now = Carbon::now();
                    $twoHoursAgo = $now->subHours(2);

                    $Txn = Txn::where('txn_no', $request->txn)->where('api_id', $api_id)->where('partner_transection_id', $order->partner_transection_id)->orderBy('id', 'DESC')->first();
                    if (!$Txn) {
                        $Txn = new Txn();
                        $Txn->txn_no = $request->txn;
                        $Txn->partner_transection_id = $order->partner_transection_id;
                        $Txn->api_id = $api_id;
                        $Txn->save();
                    }

                    $order->try = $order->try + 1;
                    $order->save();






                    DB::beginTransaction();
                    $payment_record = Payment::where('txn_id', $request->txn)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    if (!$payment_record) {
                        $processing = 1;
                        $message = "Please Wait! Your Payment is Processing.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
                    }



                    if ($payment_record->status == "Complete") {
                        $processing = 2;
                        $message = "With This Transaction No. Payment Already Completed.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
                    }




                    $order = Payent::where('id', $fund->id)->whereIn('status', [0, 2])->lockForUpdate()->first();
                    if (!$order) {
                        DB::rollBack();
                        abort(404);
                    }




                    $open_user = API::where('id', $order->api_id)->lockForUpdate()->first();
                    if (!$open_user || $open_user->type != "Admin") {
                        DB::rollBack();
                        abort(404);
                    }

                    $commit = 0;
                    if ($order && $order->amount == $payment_record->amount) {
                        if ($order->status == 1) {
                            $processing = 2;
                            $message = "Your Payment is Already Verified!";
                            DB::rollBack();
                            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
                        }
                        $partner_api_key = $open_user;
                        if ($source != env('APP_WEBSITE')) {

                            $charge = str_replace(',', '', $charge);
                            $charge = (float)$charge;
                            $charge = round($charge, 2);

                            $net_amount = $payment_record->amount - $charge;
                            $partner_api_key->balance += $net_amount;
                            $partner_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $payment_record->updated_at;
                            $Log->final_amount = $net_amount;
                            $Log->balance = $partner_api_key->balance;
                            $Log->transection_type = 1;
                            $Log->transection_id = $payment_record->id;
                            $Log->partner_id = $partner_api_key->id;
                            $Log->source = 'Iframe';
                            $Log->save();
                        }


                        if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                            if(!empty($order->account_no)){
                                $payment_record->sender = $order->account_no;
                            }
                        }


                        $payment_record->status = 'Complete';
                        $order->status = 1;
                        $order->created_at = $order->created_at;
                        $order->trans_completed_date = Carbon::now();
                        $payment_record->created_at = $order->created_at;
                        $payment_record->trans_complete_date = Carbon::now();
                        $payment_record->completed_source = 'Iframe';

                        $payment_record->transaction_id = $order->id;
                        $payment_record->api_id = $api_id;
                        $payment_record->source = $source;
                        $payment_record->charge = $charge;
                        $payment_record->partner_transection_id = $order->partner_transection_id;
                        $payment_record->member_id = $order->member_id;
                        $payment_record->save();
                        $order->account_no = $payment_record->sender;
                        $order->payment_id = $payment_record->id;
                        $order->save();

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
                            $summary_log->payment_id = $payment_record->id;
                            $summary_log->total_amount = $net_amount;
                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                            $summary_log->source = 'Iframe';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
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
                            $Log->source = 'Iframe';
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
                                $summary_log->source = 'Iframe';
                                $summary_log->save();
                            }
                        }

                         $commit = 1;
                         DB::commit();


                        if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($payment_record->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $payment_record->e_wallet_name,
                                "id" => strval($payment_record->id),
                                'transaction_type' => 'Deposit',
                                "user_account_no" => strval($payment_record->sender),

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
                                        'user_account_no' => $payment_record->sender,
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

                    $processing = 1;
                    $message = "Please Wait! Your Payment is Processing.";
                    if($commit==0){
                        DB::commit();
                    }
                    return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
                }



                return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                }else{
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('processNextPayment-PartnerController Error: txn_id: '.$txn_id. ' Error: ' .$e->getMessage());


            }
        }


        return back()->with('error', $e->getMessage());

    }

    public function getaccount(Request $request){

        $this->updateLimits();
        $this->updateEWallets();




        $username = $request->username;
        $ewallet = $request->ewallet;
        $acc = $request->acc;
        $amount = $request->amount;
        $transection_id = $request->transection_id;
        $member_id = $request->member_id;
        // $e_wallet_phone_number = $request->e_wallet_phone_number;
        $gate_id = $request->gate_id;


        $logarray = [
            'username'=>$username,
            'ewallet'=>$ewallet,
            'acc'=>$acc,
            'amount'=>$amount,
            'transection_id'=>$transection_id,
            'member_id'=>$member_id,
            'gate_id'=>$gate_id,
        ];

        $jsaon =  json_encode($logarray);
        LaravelLog::info('getaccount:'.$jsaon);



        $current_time = Carbon::now('Asia/Dhaka');

       $Setting = Setting::where('name', 'last_account_active')->first();

                $recordcounts = Payment::where('gateway_id', $gate_id)
                    ->where('created_at', '>=', $Setting->value)
                    ->select('e_wallet_phone_number', DB::raw('count(*) as total'))
                    ->groupBy('e_wallet_phone_number')
                    ->pluck('total', 'e_wallet_phone_number')
                    ->toArray();

        $account = EWalletAccount::where('e_wallet_name', $ewallet)
            ->where('monthly_limit', '>', 'monthly_received')
            ->whereRaw('daily_limit - daily_received > ?', [$amount])
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
            $message = "You Can not Proceed With this E-wallet account";
            return response()->json(['status'=>'fail','message'=>$message]);
        }


        $gate = Gateway::where('id', $gate_id)->first();

        $api_key = API::where('username', $username)->where('type', 'Admin')->first();
        if ($api_key) {
            $secretKey = $api_key->secret_key;
        } else {
            $message = "Wrong API key.";
            return response()->json(['status'=>'fail','message'=>$message]);
        }
        $api_id = $api_key->id;

        $currentMonth = now()->format('Y-m');
        $sum = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');

        $charge = 0;
        $commissions = Commission::where('api_id', $api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            }
        }


        $reqAmount = $amount;
        $payable = getAmount($reqAmount - $charge);
        $final_amo = getAmount($payable * $gate->convention_rate);


        $now = Carbon::now();
        $twoHoursAgo = $now->subHours(2);
        if (!empty($transection_id) || $transection_id != "0") {
            $fund = Payment::where('partner_transection_id', $transection_id)->where('api_id', $api_key->id)->latest()->first();
            if($fund){
                if($fund->status != 2){
                    $message = "Your Transection Already Processed!";
                    return response()->json(['status'=>'fail','message'=>$message]);

                }
            }
        } else {
            $fund = Payemnt::where('gateway_id', $gate->id)->where('amount', $amount)->where('status', 2)->where('account_no', $acc)->where('api_id', $api_key->id)->where('source', 'Iframe')->where('created_at', '>=', $twoHoursAgo)->latest()->first();
        }

        $e_wallet_phone_number = $account->account_no;

        if (!$fund) {
            $fund = new Payment();
            $fund->user_id = 0;
            $fund->gateway_id = $gate->id;
            $fund->amount = $amount;
            $fund->partner_transection_id = $transection_id;
            if (isset($member_id) && !empty($member_id)) {
                $fund->member_id = $member_id;
            }

            $fund->charge = $charge;
            $fund->sender = $acc;
            $fund->rate = $gate->convention_rate;
            $fund->final_amount = getAmount($final_amo);
            $fund->btc_amount = 0;
            $fund->btc_wallet = "";
            $fund->transaction = strRandom();
            $fund->try = 0;
            $fund->status = 'Complete';
            $fund->api_id = $api_key->id;
            $fund->e_wallet_phone_number = $e_wallet_phone_number;
            $fund->source = "Iframe";
            $fund->save();
        }
        return response()->json(['status'=>'success','phone_number'=>$account->account_no,'account_type'=>$account->type,'fund_id'=>$fund->id]);
    }

    public function convertStringToNumber($string) {
        if (strpos($string, '.') !== false) {
            return (float)$string;
        } else {
            return (int)$string;
        }
    }

    public function processTransection3($username, $ewallet, $amount, $transection_id = 0, $sign = null, $member_id = null)
    {
        $amount = str_replace(',', '', $amount);
        $data = [
            'username' => $username,
            'ewallet' => $ewallet,
            'amount' => $amount,
            'transection_id' => $transection_id,
            'member_id' => $member_id,
            'phone_number' => ''
        ];

        $data_jsaon =  json_encode($data);
        LaravelLog::info('processTransection3:'.$data_jsaon);

        $message = "";
        $txn_verification = "";
        $ewalletee = strtolower($ewallet);

        if($ewalletee != "bkash"){
            return response()->json(['message' => 'You are only allowed to proceed with Bkash E-Wallet'], 404);
        }

        $api_key = API::where('username', $username)->select('id','type','secret_key','txn_verification','redirect_url','sign','api_key','min_deposit','parent_id')->first();
        if ($api_key && $api_key->type == "Admin") {
            $api_id = $api_key->id;
            $secretKey = $api_key->secret_key;
            $txn_verification = $api_key->txn_verification;
            $data['redirect_url'] = $api_key->redirect_url;



            if ($api_key->sign == 1) {
                if (isset($sign) && !empty($sign)) {
                    $string_to_hash = json_encode(array(
                        "amount" => $amount,
                        "api_key" => $api_key->api_key,
                        "e_wallet_name" => $ewallet
                    ));

                    $hash = hash("sha256", $string_to_hash);
                    $hmac = hash_hmac('sha256', $hash, $secretKey);

                    $timestamp = time();
                    $timestamp_str = (string) $timestamp;
                    $timestamp_length = strlen($timestamp_str);
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
            return response()->json(['message' => 'Wrong Username OR Username Not Exist.'], 404);
        }


        $gate = Gateway::where('code', $ewallet)->where('status', 1)->first();

        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return response()->json(['message' => $message], 404);
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount,2);
            return response()->json(['message' => $message], 404);
        }


        $sum = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');

        $charge = 0;
        $commissions = Commission::where('api_id', $api_key->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->m_deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->m_deposit_percentage * $amount / 100;
            }
        }


        $reqAmount = $amount;
        $payable = getAmount($reqAmount - $charge);
        $final_amo = getAmount($payable * $gate->convention_rate);


        $fund = Payment::where('partner_transection_id', $transection_id)->where('api_id', $api_key->id)->latest()->first();
        if($fund){
            $message = "This partner transaction ID has already been created!";
            return response()->json(['status'=>'fail','message'=>$message]);
        }


        if (!empty($transection_id) || $transection_id != "0") {
            $fund = Payment::where('partner_transection_id', $transection_id)->where('api_id', $api_key->id)->latest()->first();
            if($fund){
                $message = "This partner transaction ID has already been created!";
                return response()->json(['status'=>'fail','message'=>$message]);
            }

            $merchantinvoice = $transection_id;
        } else {

            $merchantinvoice = "Invoice".time();
        }


        $Setting = Setting::where('name', 'last_merchant_account_active')->first();

        $recordcounts = Payment::where('gateway_id', $gate->id)
            ->where('created_at', '>=', $Setting->value)
            ->whereNotNull('live_payment_id')
            ->where('live_payment_id', '!=', '')
            ->select('e_wallet_phone_number', DB::raw('count(*) as total'))
            ->groupBy('e_wallet_phone_number')
            ->pluck('total', 'e_wallet_phone_number')
            ->toArray();

        $account = MerchantAccount::where('status', 1)
            ->get()
            ->sortBy(function ($single_account) use ($recordcounts) {
                return $recordcounts[$single_account->e_wallet_phone_number] ?? 0;
            })
            ->values()->first();

            if (!$account) {
                return response()->json(['error' => 'This gateway has been deactivated by the Administrator.'], 422);
            }



        $e_wallet_phone_number = $account->e_wallet_phone_number;


        $fund = new Payment();
        $fund->user_id = 0;
        $fund->gateway_id = $gate->id;
        $fund->amount = $amount;
        $fund->partner_transection_id = $transection_id;
        if (isset($member_id) && !empty($member_id)) {
            $fund->member_id = $member_id;
        }

        $fund->charge = $charge;
        // $fund->account_no = $acc;
        $fund->transaction = strRandom();
        $fund->try = 0;
        $fund->status = "Complete";
        $fund->api_id = $api_key->id;
        $fund->e_wallet_phone_number = $e_wallet_phone_number;
        $fund->request_source = "Iframe";
        $fund->e_wallet_name = $gate->name;
        $fund->sender = '';


        $accessToken = $this->getBkashToken($account);
        $createBkashPayment = $this->createBkashPayment($accessToken, $amount, $merchantinvoice, $account);


        if(isset($createBkashPayment['paymentID'])){
            $fund->live_payment_id = $createBkashPayment['paymentID'];
            $fund->save();


            if ($charge > 0 && $api_key->parent_id > 0) {
                // $parent_commissions = Commission::where('id', $commissions->parent_id)->first();
                if ($commissions->parent_id > 0 && $commissions->m_parent_deposit_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $api_key->parent_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->m_deposit_percentage;
                    $profit_p = $commissions->m_parent_deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();

                    //  $main_parent_commissions = Commission::where('id', $parent_commissions->parent_id)->first();

                }

                if ($commissions->parent2_id > 0 && $commissions->m_parent2_deposit_percentage > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $commissions->parent2_id;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->m_deposit_percentage;
                    $profit_p = $commissions->m_parent2_deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }

            return redirect()->away($createBkashPayment['bkashURL']);
        }else{
            return response()->json(['message' => 'Gateway Error.'], 404);
        }
        exit;

    }

      public function getBkashToken($account) {
        $url = env('BKASH_BASE_URL') . "/checkout/token/grant";

        $data = [
            "app_key" => $account->app_key,
            "app_secret" => $account->app_secret,
        ];

        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "username" => $account->username,
            "password" => $account->password,
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        if ($response->successful()) {
            $result = $response->json();
            return $result['id_token'] ?? null;
        }

        return null;
    }

    public function createBkashPayment($accessToken, $amount, $merchantinvoice, $account) {
        $url = env('BKASH_BASE_URL') . "/checkout/create";

        $data = [
            "mode" => "0011",
            "payerReference" => " ",
            "callbackURL" => env('BKASH_CALLBACK_URL', 'https://ecpay.asia/api/bkashcallback'),
            "amount" => $amount,
            "currency" => "BDT",
            "intent" => "sale",
            "merchantInvoiceNumber" => $merchantinvoice,
        ];

        $headers = [
            "Authorization" => "Bearer " . $accessToken,
            "X-App-Key" => $account->app_key,
        ];

        $response = Http::withHeaders($headers)
            ->acceptJson()
            ->post($url, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }


    public function executeBkashPayment($accessToken, $paymentID, $account) {

        $url = env('BKASH_BASE_URL') . "/checkout/execute";

        $data = [
            "paymentID" => $paymentID
        ];

        $headers = [
            "Authorization" => "Bearer " . $accessToken,
            "X-App-Key" => $account->app_key,
        ];

        $response = Http::withHeaders($headers)
            ->acceptJson()
            ->post($url, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }


    public function queryBkashPayment($accessToken, $paymentID) {
        $url = env('BKASH_BASE_URL') . "/checkout/payment/status";

        $data = ["paymentID" => $paymentID];

        $headers = [
            "Authorization" => "Bearer " . $accessToken,
            "X-App-Key" => env('BKASH_APP_KEY'),
        ];

        $response = Http::withHeaders($headers)
            ->acceptJson()
            ->post($url, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }



}
