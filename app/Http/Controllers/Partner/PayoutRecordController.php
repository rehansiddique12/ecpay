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
use App\Models\PendingPayment;
use App\Services\BasicService;
use App\Models\MerchantAccount;
use Illuminate\Validation\Rule;
use App\Models\ParentCommission;
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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PartnerBalanceExportForPartner;
use Illuminate\Support\Facades\Log as LaravelLog;

class PayoutRecordController extends Controller
{
    public function methods($username)
    {

        $open_user = API::where('username', $username)->where('status', 1)->first();
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
        $open_user = API::where('username', $username)->where('status', 1)->first();
        if ($open_user && $open_user->type == "Admin") {
            $min_deposit = $open_user->min_deposit;
            if (session()->get('plan_id') != null) {
                return redirect(route('user.payment'));
            }
            $totalPayment = null;
            $gateways = Gateway::where('status', 1)->where('deposit_on' ,1)
                ->whereHas('category', function ($query) {
                    $query->where('name', 'ewallet');
                })
                ->with('category')
                ->get();
            // dd($gateways);
            return view('partner.payout.depositFund', compact('totalPayment', 'gateways', 'username', 'min_deposit'));
        } else {
            abort(404);
        }
    }


    public function payoutMoneyTransection($username)
    {
        $open_user = API::where('username', $username)->where('status', 1)->first();
        if ($open_user && $open_user->type == "Admin") {
            $min_withdrawal = $open_user->min_withdrawal;
            $title = "Payout Money";
            $gateways = Gateway::where('status', 1)->where('withdrawal_on' , 1)
                ->whereHas('category', function ($query) {
                    $query->where('name', 'ewallet');
                })
                ->with('category')
                ->get();
            return view('partner.payout.moneyopen', compact('title', 'gateways', 'username', 'min_withdrawal'));
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
            $open_user = API::where('username', $request->username)->where('status', 1)->lockForUpdate()->first();
            if (!$open_user || $open_user->type != "Admin") {
                DB::rollBack();
                return response()->json(['error' => 'Contact with Admin or your link provider'], 422);
            }

            $user_account_no = "";
            if ($open_user->txn_verification == 0) {
                $acc = $request->account_no;
                $ewalletee = strtolower($request->gateway);

                if (!is_numeric($acc)) {
                    return response()->json(['code' => 605, 'error' => 'Account number formate not valid'], 404);
                }

                if (substr($acc, 0, 2) === "01") {
                    $num_digits = strlen($acc);
                    if ($ewalletee == 'bkash' && $num_digits != 11) {
                        DB::rollBack();
                        return response()->json(['code' => 605, 'error' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'nagad' && $num_digits != 11) {
                        DB::rollBack();
                        return response()->json(['code' => 605, 'error' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'rocket' && ($num_digits < 11 || $num_digits > 12)) {
                        DB::rollBack();
                        return response()->json(['code' => 605, 'error' => 'Account number should be 11 or 12 digit'], 404);
                    }
                } else {
                    DB::rollBack();
                    return response()->json(['code' => 605, 'error' => 'Account number should start from 01'], 404);
                }

                $user_account_no = $request->account_no;
            } elseif ($request->filled('account_no')) {
                $user_account_no = $request->account_no;
            }

            if ($open_user->min_deposit > $request->amount) {
                DB::rollBack();
                return response()->json(['error' => 'Min Deposit Limit is ' . $open_user->min_deposit], 422);
            }

            $basic = (object)config('basic');
            $gate = Gateway::where('code', $request->gateway)->where('status', 1)->where('deposit_on' ,1)->first();
            if (!$gate) {
                DB::rollBack();
                return response()->json(['error' => 'Invalid Gateway'], 422);
            }

            $this->updateLimits();
            $this->updateEWallets();

            $current_time = Carbon::now('Asia/Dhaka');

            $Setting = Setting::where('name', 'last_account_active')->first();

            $now = Carbon::now();
            $startOfToday = $now->copy()->startOfDay();
            $startOfMonth = $now->copy()->startOfMonth();
            $oneMinuteAgo = $now->copy()->subMinute();

            // Query
            $results = Payment::selectRaw('
                e_wallet_phone_number,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) AS counts_for_round_robin,
                COUNT(CASE WHEN trans_complete_date >= ? AND status = "Complete" THEN 1 END) AS today_count,
                COUNT(CASE WHEN trans_complete_date >= ? AND status = "Complete" THEN 1 END) AS month_count,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) AS one_min_count,
                SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) AS one_min_sum
            ', [
                $Setting->value,
                $startOfToday,
                $startOfMonth,
                $oneMinuteAgo,
                $oneMinuteAgo
            ])
                ->where('gateway_id', $gate->id)
                ->whereNotNull('e_wallet_phone_number')
                ->where('e_wallet_phone_number', '!=', '')
                ->groupBy('e_wallet_phone_number')
                ->get();


            // ->where('created_at', '>=', $startOfMonth)

            $all_accounts = [];

            foreach ($results as $result) {
                $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
                $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
                $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
                $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
                $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
            }

            $account = EWalletAccount::where('e_wallet_name', $gate->name)
                ->where('monthly_limit', '>', 'monthly_received')
                ->whereRaw('daily_limit - daily_received > ?', [$request->amount])
                ->where('status', 1)
                ->whereIn('account_type', ['Deposit', 'Both'])
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
                return response()->json(['error' => 'You Can not Proceed With this E-wallet account'], 422);
            }

            $reqAmount = $request->amount;
            $amount = $request->amount;
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


            $commissions = Commission::where('category_id', $user->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $request->amount / 100;
            } else {
                $commissions = Commission::where('category_id', $user->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                if ($commissions) {
                    $charge = $commissions->deposit_percentage * $request->amount / 100;
                }
            }

            $payable = getAmount($reqAmount - $charge);
            $final_amo = getAmount($payable * $gate->convention_rate);
            $account_no = $user_account_no;
            $e_wallet_phone_number = $account->account_no;

            $fund = $this->newFundOpen($request, $gate, $charge, $final_amo, $reqAmount, $account_no, $open_user, $e_wallet_phone_number);

            $parentIds = ParentCommission::where('user_id', $user->id)
                ->pluck('parent_id')
                ->unique()
                ->values();
            foreach ($parentIds as  $parentId) {

                $parent_charge = 0;

                $parent_commission = ParentCommission::where('user_id', $user->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($parent_commission) {
                    $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                } else {
                    $parent_commission = ParentCommission::where('user_id', $user->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                    }
                }

                if ($parent_charge > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $user->id;
                    $PartnerCommission->from_id = $parentId;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                    $profit_p = $parent_commission->deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }


            //start
            $commit = 0;
            if ($fund && $user->txn_verification == 0) {
                $order = Payment::where('id', $fund['id'])->first();
                $payment = PendingPayment::where('e_wallet_name', $gate->code)
                    ->where('status', 0)
                    ->where('amount', $fund['amount'])
                    ->where('sender', $fund['account_no'])
                    ->where('created_at', '>=', Carbon::now()->subHours(2))
                    ->orderBy('id', 'DESC')
                    ->first();
                if (!$payment) {
                    $payment = PendingPayment::where('e_wallet_name', $gate->code)
                        ->where('status', 0)
                        ->where('amount', $fund['amount'])
                        ->where('sender', 'LIKE', substr($fund['account_no'], 0, 4) . '%')
                        ->where('sender', 'LIKE', '%' . substr($fund['account_no'], -3))
                        ->where('sender', 'LIKE', '%XXXX%')
                        ->where('created_at', '>=', Carbon::now()->subHours(2))
                        ->where('mac_address', '111.111.11.111')
                        ->orderBy('id', 'DESC')
                        ->first();
                    if ($payment) {
                        $payment->sender = $fund['account_no'];
                    }
                }
                if ($payment) {

                    $check_payment_txn = Payment::where('txn_id', $payment->txn_id)->first();
                    if ($check_payment_txn) {
                        DB::rollBack();
                        return response()->json(['error' => 'By This Txn no, Payment Already Completed.']);
                    }

                    $net_amount = $reqAmount - $charge;
                    $open_user->balance += $net_amount;
                    $open_user->save();

                    $Log = new Log();
                    $Log->date_time = $payment->updated_at;
                    $Log->final_amount = $net_amount;
                    $Log->balance = $open_user->balance;
                    $Log->transection_type = 1;
                    $Log->transection_id = $order->id;
                    $Log->partner_id = $open_user->id;
                    $Log->source = 'PartnerLink';
                    $Log->save();


                    if (empty($order->sender) || $order->sender == 0) {
                        $order->sender = $payment->sender;
                    }

                    $order->txn_id = $payment->txn_id;
                    $order->date_time = $payment->date_time;
                    $order->transaction_type = $payment->transaction_type;
                    $order->ip_address = $payment->ip_address;
                    $order->e_wallet_type = $payment->e_wallet_type;
                    $order->mac_address = $payment->mac_address;
                    $order->fee = $payment->fee;
                    $order->commission = $payment->commission;
                    $order->e_wallet_charges = $payment->e_wallet_charges;
                    $order->payment_received_at = $payment->created_at;


                    $order->charge = $charge;
                    $order->status = 'Complete';
                    $order->trans_complete_date = Carbon::now();
                    $order->completed_source = 'PartnerLink';

                    $payment->status = 1;
                    $payment->save();
                    $payment = null;
                    // $payment->delete();

                    $order->api_id = $api_id;
                    $order->save();

                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $order->api_id)->whereDate('created_at', '>=', $order->created_at)->get();
                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                        $amount_to_update = round($amount_to_update, 2);
                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                        $DailyPartnerSummary_record->save();

                        $summary_log = new DailyPartnerSummaryLog();
                        $summary_log->partner_id = $open_user->id;
                        $summary_log->partner_balance = $open_user->balance;
                        $summary_log->payment_id = $order->id;
                        $summary_log->total_amount = $net_amount;
                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                        $summary_log->source = 'PartnerLink';
                        $summary_log->save();
                    }

                    $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                    foreach ($PartnerCommissions as $PartnerCommission) {
                        $PartnerCommission->status = 1;
                        $PartnerCommission->save();
                        $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                        if ($parent_api_key) {
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
                                $summary_log->source = 'PartnerLink';
                                $summary_log->save();
                            }
                        }
                    }

                    $commit = 1;
                    DB::commit();

                    if (!empty($open_user->api_endpoint_deposit) && $open_user->website != env('APP_WEBSITE')) {

                        $array_data = [
                            'id' => $order->id,
                            'partner_transection_id' => $order->partner_transection_id,
                            'transaction_type' => 'Deposit',
                            'e_wallet_name' => $order->e_wallet_name,
                            'amount' => $this->convertStringToNumber($order->amount),
                            'user_account_no' => $order->sender,
                            'txn_id' => $order->txn_id,
                            'e_wallet_phone_number' => $order->e_wallet_phone_number,
                            'e_wallet_type' => $order->e_wallet_type,
                            'charges' => $this->convertStringToNumber($order->charge),
                            'status' => $order->status,
                            'completion_date' => Carbon::parse($order->date_time)->toDateString(),
                            'completion_time' => Carbon::parse($order->date_time)->toTimeString(),
                            'created_at' => $order->created_at,
                            'updated_at' => $order->updated_at,
                            'sign' => $order->sign,
                        ];

                        if (!empty($order->member_id)) {
                            $array_data['member_id'] = $order->member_id;
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
            if ($commit == 0) {
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

        $logo = "";
        $banner = "";

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
        $gate = Gateway::where('code', $ewallet)->where('status', 1)->where('deposit_on' , 1)->first();
        if (!$gate) {
            $message = "Gateway is inactive.";
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        $api_key = API::where('username', $username)->where('status', 1)->first();
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


        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount, 2);
            return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }




        $now = Carbon::now();
        $twoHoursAgo = $now->subHours(2);
        if (!empty($transection_id) || $transection_id != "0") {
            $fund = Payment::where('partner_transection_id', $transection_id)->where('api_id', $api_key->id)->latest()->first();
            if ($fund) {
                if ($fund->status != "Pending") {
                    $message = "Your Transection Already Processed!";
                    return view('partner.payout.process_transection', compact('data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
                }
            }
        } else {
            $fund = Payment::where('gateway_id', $gate->id)->where('amount', $amount)->where('status', 'Pending')->where('sender', $acc)->where('api_id', $api_key->id)->where('created_at', '>=', $twoHoursAgo)->latest()->first();
        }

        if (!$fund) {

            $this->updateLimits();
            $this->updateEWallets();

            $current_time = Carbon::now('Asia/Dhaka');

            $Setting = Setting::where('name', 'last_account_active')->first();

            // $recordcounts = Payment::where('gateway_id', $gate->id)
            //     ->where('created_at', '>=', $Setting->value)
            //     ->select('e_wallet_phone_number', DB::raw('count(*) as total'))
            //     ->groupBy('e_wallet_phone_number')
            //     ->pluck('total', 'e_wallet_phone_number')
            //     ->toArray();

            // Define time ranges

            ////////////////////////////////
            $now = Carbon::now();
            $startOfToday = $now->copy()->startOfDay();
            $startOfMonth = $now->copy()->startOfMonth();
            $oneMinuteAgo = $now->copy()->subMinute();

            // Query
            $results = Payment::selectRaw('
                        e_wallet_phone_number,
                        COUNT(CASE WHEN created_at >= ? THEN 1 END) AS counts_for_round_robin,
                        COUNT(CASE WHEN trans_complete_date >= ? AND status = "Complete" THEN 1 END) AS today_count,
                        COUNT(CASE WHEN trans_complete_date >= ? AND status = "Complete" THEN 1 END) AS month_count,
                        COUNT(CASE WHEN created_at >= ? THEN 1 END) AS one_min_count,
                        SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) AS one_min_sum
                    ', [
                $Setting->value,
                $startOfToday,
                $startOfMonth,
                $oneMinuteAgo,
                $oneMinuteAgo
            ])
                ->where('gateway_id', $gate->id)
                ->whereNotNull('e_wallet_phone_number')
                ->where('e_wallet_phone_number', '!=', '')
                ->groupBy('e_wallet_phone_number')
                ->get();


            // ->where('created_at', '>=', $startOfMonth)

            $all_accounts = [];

            foreach ($results as $result) {
                $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
                $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
                $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
                $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
                $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
            }



            $account = EWalletAccount::where('e_wallet_name', $ewallet)
                ->where('monthly_limit', '>', 'monthly_received')
                ->whereRaw('daily_limit - daily_received > ?', [$amount])
                ->where('status', 1)
                ->whereIn('account_type', ['Deposit', 'Both'])
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


            // dd($account);

            /////////////////////////////////////////////


            // $account = EWalletAccount::where('e_wallet_name', $ewallet)
            //     ->where('monthly_limit', '>', 'monthly_received')
            //     ->whereRaw('daily_limit - daily_received > ?', [$amount])
            //     ->where('status', 1)
            //     ->whereIn('account_type', ['Deposit', 'Both'])
            //     ->where(function ($query) use ($current_time) {
            //         $query->where('apply_time_limit', 0)
            //             ->orWhere(function ($query) use ($current_time) {
            //                 $query->where('apply_time_limit', 1)
            //                     ->where('from_time', '<=', $current_time)
            //                     ->where('to_time', '>=', $current_time);
            //             });
            //     })
            //     ->get()
            //     ->sortBy(function ($single_account) use ($all_accounts) {
            //         return $all_accounts[$single_account->account_no]['counts_for_round_robin'] ?? 0;
            //     })
            //     ->values()->first();




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

            $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            } else {
                $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
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
            $fund->status = 'Pending';
            $fund->api_id = $api_key->id;
            $fund->e_wallet_phone_number = $e_wallet_phone_number;
            $fund->request_source = "Iframe-1";
            $fund->save();



            $parentIds = ParentCommission::where('user_id', $api_key->id)
                ->pluck('parent_id')
                ->unique()
                ->values();


            foreach ($parentIds as  $parentId) {

                $parent_charge = 0;



                $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($parent_commission) {
                    $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                } else {
                    $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                    }
                }





                if ($parent_charge > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $parentId;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                    $profit_p = $parent_commission->deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }
        } else {

            if ($fund->gateway_id != $gate->id || $fund->amount != $amount) {
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
        $e_wallet_accounts = EWalletAccount::select('last_limit_reset', 'daily_received', 'daily_sent', 'monthly_received', 'monthly_sent')->get();
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
        $records = EWalletAccount::select('e_wallet_name', 'account_no', 'is_live')->get();
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
        $maxAttempts = 5;
        $attempt = 0;
        $success = 0;
        $txn_id = "";
        if ($request->filled('txn')) {
            $txn_id = $request->txn;
        }



        while ($attempt < $maxAttempts && $success == 0) {

            LaravelLog::info('processNextPayment-PartnerController try(' . $attempt + 1 . ') txn_id: ' . $txn_id);

            DB::beginTransaction();
            try {
                $message = "";
                $processing = 1;
                $remainingTime = 0;
                $url = "";
                // dd($id);
                $order = Payment::where('id', $id)->with(['gateway'])->where('status', 'Pending')->lockForUpdate()->first();
                if (!$order) {
                    DB::rollBack();
                    abort(404);
                }
                $ewallet = strtolower($order->gateway->code);





                if ($order->status == "Complete") {
                    $processing = 2;
                    $message = "With This Transaction No. Payment Already Completed.";
                    DB::commit();
                    return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
                }

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
                    return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
                }

                $open_user = API::where('id', $order->api_id)->where('status', 1)->lockForUpdate()->first();
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




                    $payment_record = PendingPayment::where('txn_id', $request->txn)->where('status', 0)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    if (!$payment_record) {
                        $processing = 1;
                        $message = "Please Wait! Your Payment is Processing.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
                    } else {
                        $check_payment_txn = Payment::where('txn_id', $payment_record->txn_id)->first();
                        if ($check_payment_txn) {
                            DB::rollBack();
                            $message = "By This Txn no, Payment Already Completed.";
                            return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
                        }
                    }

                    $charge = 0;
                    $commit = 0;
                    if ($order && $order->amount == $payment_record->amount) {
                        if ($order->status == "Complete") {
                            $processing = 2;
                            $message = "Your Payment is Already Verified!";
                            DB::rollBack();
                            return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
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
                                ->where('status', 1)
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

                            $net_amount = $payment_record->amount - $charge;
                            $partner_api_key->balance += $net_amount;
                            $partner_api_key->save();




                            $Log = new Log();
                            $Log->date_time = $payment_record->updated_at;
                            $Log->final_amount = $net_amount;
                            $Log->balance = $partner_api_key->balance;
                            $Log->transection_type = 1;
                            $Log->transection_id = $order->id;
                            $Log->partner_id = $partner_api_key->id;
                            $Log->source = 'Iframe-1';
                            $Log->save();
                        }

                        // if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        //     if(!empty($order->account_no)){
                        //         $payment_record->sender = $order->account_no;
                        //     }
                        // }


                        if (empty($order->sender) || $order->sender == 0) {
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



                        $order->status = 'Complete';
                        $order->trans_complete_date = Carbon::now();
                        $order->completed_source = 'Iframe-1';
                        $order->charge = $charge;
                        $order->save();

                        // $order->status = 1;
                        // $order->created_at = $order->created_at;
                        // $order->trans_completed_date = Carbon::now();
                        // $payment_record->created_at = $order->created_at;
                        // $payment_record->transaction_id = $order->id;
                        // $payment_record->api_id = $api_id;
                        // $payment_record->source = $source;
                        // $payment_record->partner_transection_id = $order->partner_transection_id;
                        // $payment_record->member_id = $order->member_id;
                        // $order->account_no = $payment_record->sender;
                        // $order->payment_id = $payment_record->id;

                        $payment_record->status = 1;
                        $payment_record->save();
                        $payment_record = null;
                        // $payment_record->delete();

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
                            $summary_log->source = 'Iframe-1';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                        foreach ($PartnerCommissions as $PartnerCommission) {
                            $PartnerCommission->status = 1;
                            $PartnerCommission->save();
                            $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                            if ($parent_api_key) {
                                $parent_api_key->balance += $PartnerCommission->profit;
                                $parent_api_key->save();

                                $Log = new Log();
                                $Log->date_time = $PartnerCommission->created_at;
                                $Log->final_amount = $PartnerCommission->profit;
                                $Log->balance = $parent_api_key->balance;
                                $Log->transection_type = 5;
                                $Log->transection_id = $PartnerCommission->id;
                                $Log->partner_id = $PartnerCommission->from_id;
                                $Log->source = 'Iframe-1';
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
                                    $summary_log->source = 'Iframe-1';
                                    $summary_log->save();
                                }
                            }
                        }

                        DB::commit();
                        $commit = 1;


                        if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($order->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $order->e_wallet_name,
                                "id" => strval($order->id),
                                'transaction_type' => 'Deposit',
                                "user_account_no" => strval($order->sender),

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
                                'user_account_no' => $order->sender,
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

                            if (!empty($order->member_id)) {
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

                    $processing = 1;
                    $message = "Please Wait! Your Payment is Processing.";
                    if ($commit == 0) {
                        DB::commit();
                    }

                    return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
                }
                DB::commit();
                return view('partner.payout.paymentProcessingIframe', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url'));
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                } else {
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('processNextPayment-PartnerController Error: txn_id: ' . $txn_id . 'seccess ' . $success . ' Error: ' . $e->getMessage());
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

        $order = "";
        $logo = "";
        $banner = "";

        $fund = Payment::where('id', $request->id)->latest()->first();
        $fiveMinutesAgo = Carbon::now()->subMinutes(5)->timestamp;
        if (isset($request->time) && $request->time > $fiveMinutesAgo) {
            $remainingTime = $request->time - $fiveMinutesAgo;
        }

        if ($fund->status == "Complete") {
            $processing = 2;
            $message = "With This Transaction No. Payment Already Completed.";
            DB::commit();
            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
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



        $payment_record = PendingPayment::where('txn_id', $request->txn)->where('status', 0)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
        if (!$payment_record) {
            $processing = 1;
            $message = "Please Wait! Your Payment is Processing.";
            DB::commit();
            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
        } else {
            $check_payment_txn = Payment::where('txn_id', $payment_record->txn_id)->first();
            if ($check_payment_txn) {
                DB::rollBack();
                $message = "By This Txn no, Payment Already Completed.";
                return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
            }
        }




        $order = Payment::where('id', $fund->id)->where('status', 'Pending')->lockForUpdate()->first();
        if (!$order) {
            DB::rollBack();
            abort(404);
        }




        $open_user = API::where('id', $order->api_id)->where('status', 1)->lockForUpdate()->first();
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
            if ($order->status == "Complete") {
                $processing = 2;
                $message = "Your Payment is Already Verified!";
                DB::rollBack();
                return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
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
                $Log->transection_id = $order->id;
                $Log->partner_id = $partner_api_key->id;
                $Log->source = 'Iframe-2';
                $Log->save();
            }

            // if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
            //     if(!empty($order->account_no)){
            //         $payment_record->sender = $order->account_no;
            //     }
            // }

            $order->status = 'Complete';
            $order->created_at = $order->created_at;
            // $order->trans_completed_date = Carbon::now();
            // $payment_record->created_at = $order->created_at;
            // $payment_record->trans_complete_date = Carbon::now();
            $order->completed_source = 'Iframe-2';

            // $payment_record->api_id = $api_id;
            // $payment_record->request_source  = $source;
            $order->charge = $charge;
            // $payment_record->partner_transection_id = $order->partner_transection_id;
            // $payment_record->member_id = $order->member_id;

            if (empty($order->sender) || $order->sender == 0) {
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



            $order->save();

            $payment_record->status = 1;
            $payment_record->save();
            $payment_record = null;
            //    $payment_record->delete();

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
                $summary_log->source = 'Iframe-2';
                $summary_log->save();
            }

            $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
            foreach ($PartnerCommissions as $PartnerCommission) {
                $PartnerCommission->status = 1;
                $PartnerCommission->save();
                $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                if ($parent_api_key) {
                    $parent_api_key->balance += $PartnerCommission->profit;
                    $parent_api_key->save();

                    $Log = new Log();
                    $Log->date_time = $PartnerCommission->created_at;
                    $Log->final_amount = $PartnerCommission->profit;
                    $Log->balance = $parent_api_key->balance;
                    $Log->transection_type = 5;
                    $Log->transection_id = $PartnerCommission->id;
                    $Log->partner_id = $PartnerCommission->from_id;
                    $Log->source = 'Iframe-2';
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
                        $summary_log->source = 'Iframe-2';
                        $summary_log->save();
                    }
                }
            }

            $commit = 1;
            DB::commit();


            if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                $string_to_hash = json_encode(array(
                    "amount" => strval($this->convertStringToNumber($order->amount)),
                    "api_key" => $partner_api_key->api_key,
                    "e_wallet_name" => $order->e_wallet_name,
                    "id" => strval($order->id),
                    'transaction_type' => 'Deposit',
                    "user_account_no" => strval($order->sender),

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
                    'user_account_no' => $order->sender,
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

                if (!empty($order->member_id)) {
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

        $processing = 1;
        $message = "Please Wait! Your Payment is Processing.";
        if ($commit == 0) {
            DB::commit();
        }
        return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
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


        $data['account_type'] = "";

        $data_jsaon =  json_encode($data);
        LaravelLog::info('processTransection2:' . $data_jsaon);

        $message = "";
        $banner = "";
        $txn_verification = "";
        $ewalletee = strtolower($ewallet);

        $logo = "";
        $ewallet_to_show = "";

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

        $gate = Gateway::where('code', $ewallet)->where('status', 1)->where('deposit_on' , 1)->first();
        if (!$gate) {
            $message = "Gateway is inactive.";
            return view('partner.payout.process_transection2', compact('ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }

        $api_key = API::where('username', $username)->where('status', 1)->select('id', 'type', 'secret_key', 'txn_verification', 'redirect_url', 'sign', 'api_key', 'min_deposit', 'parent_id')->first();
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
            return view('partner.payout.process_transection2', compact('ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }




        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return view('partner.payout.process_transection2', compact('ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount, 2);
            return view('partner.payout.process_transection2', compact('ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        $data['gate_id'] = $gate->id;
        $data['phone_number'] = "Loading...";


        // setting for theme style
        return view('partner.payout.process_transection2', compact('ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
    }


    public function processTransection4($username, $ewallet, $acc, $amount, $transection_id = 0, $sign = null, $member_id = null)
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


        $data['account_type'] = "";

        $data_jsaon =  json_encode($data);
        LaravelLog::info('processTransection2:' . $data_jsaon);

        $message = "";
        $banner = "";
        $txn_verification = "";
        $ewalletee = strtolower($ewallet);

        $logo = "";
        $ewallet_to_show = "";
        $ewallet_to_show_bangla = "";

        if ($ewalletee == 'bkash') {
            $logo = asset('assets/images/bkash6.png');
            $ewallet_to_show = "bKash";
            $ewallet_to_show_bangla = "বিকাশ";
        }
        if ($ewalletee == 'nagad') {
            $logo = asset('assets/images/nagad6.png');
            $ewallet_to_show = "Nagad";
            $ewallet_to_show_bangla = "নগদ";
        }
        if ($ewalletee == 'rocket') {
            $logo = asset('assets/images/rocket4.png');
            $ewallet_to_show = "Rocket";
            $ewallet_to_show_bangla = "রকেট";
        }

        $gate = Gateway::where('code', $ewallet)->where('status', 1)->where('deposit_on' , 1)->first();
        if (!$gate) {
            $message = "Gateway is inactive.";
            return view('partner.payout.process_transection4', compact('ewallet_to_show_bangla','ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }

        $api_key = API::where('username', $username)->where('status', 1)->select('id', 'type', 'secret_key', 'txn_verification', 'redirect_url', 'sign', 'api_key', 'min_deposit', 'parent_id')->first();
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
            return view('partner.payout.process_transection4', compact('ewallet_to_show_bangla','ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }




        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return view('partner.payout.process_transection4', compact('ewallet_to_show_bangla','ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount, 2);
            return view('partner.payout.process_transection4', compact('ewallet_to_show_bangla','ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
        }


        $data['gate_id'] = $gate->id;
        $data['phone_number'] = "Loading......";


        // setting for theme style
        return view('partner.payout.process_transection4', compact('ewallet_to_show_bangla','ewallet_to_show', 'data', 'message', 'ewallet', 'logo', 'banner', 'txn_verification', 'remainingTime'));
    }

    public function processNextPayment2(Request $request)
    {

        $username = $request->username;
        $ewallet = $request->ewallet;
        $amount = $request->amount;
        $fund_id = $request->fund_id;



        $api_key = API::where('username', $username)->where('status', 1)->where('type', 'Admin')->first();
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

        $fund = Payment::where('id', $fund_id)->latest()->first();
        $account = EWalletAccount::where('e_wallet_name', $fund->e_wallet_name)
            ->where('account_no', $fund->e_wallet_phone_number)
            ->where('status', 1)
            ->first();


        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
        if ($commissions) {
            $charge = $commissions->deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            }
        }



        if ($fund) {

            $parentIds = ParentCommission::where('user_id', $api_key->id)
                ->pluck('parent_id')
                ->unique()
                ->values();
            foreach ($parentIds as  $parentId) {

                $parent_charge = 0;

                $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($parent_commission) {
                    $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                } else {
                    $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                    }
                }

                if ($parent_charge > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $parentId;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                    $profit_p = $parent_commission->deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }
        } else {
            $message = "Your Transection Already Processed!";
            return back()->with('error', $message);
        }

        $maxAttempts = 5;
        $attempt = 0;
        $success = 0;

        $txn_id = "";
        if ($request->filled('txn')) {
            $txn_id = $request->txn;
        }





        while ($attempt < $maxAttempts && $success == 0) {
            LaravelLog::info('processNextPayment-PartnerController try(' . $attempt + 1 . ') txn_id: ' . $txn_id);
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
                    $payment_record = PendingPayment::where('txn_id', $request->txn)->where('status', 0)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    if (!$payment_record) {
                        $processing = 1;
                        $message = "Please Wait! Your Payment is Processing.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                    } else {
                        $check_payment_txn = Payment::where('txn_id', $payment_record->txn_id)->first();
                        if ($check_payment_txn) {
                            DB::rollBack();
                            $message = "By This Txn no, Payment Already Completed.";
                            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                        }
                    }



                    if ($order->status == "Complete") {
                        $processing = 2;
                        $message = "With This Transaction No. Payment Already Completed.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                    }




                    $order = Payment::where('id', $fund->id)->where('status', 'Pending')->lockForUpdate()->first();
                    if (!$order) {
                        DB::rollBack();
                        abort(404);
                    }




                    $open_user = API::where('id', $order->api_id)->where('status', 1)->lockForUpdate()->first();
                    if (!$open_user || $open_user->type != "Admin") {
                        DB::rollBack();
                        abort(404);
                    }

                    $commit = 0;
                    if ($order && $order->amount == $payment_record->amount) {
                        if ($order->status == "Complete") {
                            $processing = 2;
                            $message = "Your Payment is Already Verified!";
                            DB::rollBack();
                            return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
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
                            $Log->transection_id = $order->id;
                            $Log->partner_id = $partner_api_key->id;
                            $Log->source = 'Iframe-2';
                            $Log->save();
                        }


                        // if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        //     if(!empty($order->account_no)){
                        //         $payment_record->sender = $order->account_no;
                        //     }
                        // }


                        $order->status = 'Complete';
                        $order->trans_complete_date = Carbon::now();
                        $order->completed_source = 'Iframe-2';
                        $order->charge = $charge;

                        if (empty($order->sender) || $order->sender == 0) {
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


                        $order->save();

                        $payment_record->status = 1;
                        $payment_record->save();
                        $payment_record = null;
                        // $payment_record->delete();

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
                            $summary_log->source = 'Iframe-2';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                        foreach ($PartnerCommissions as $PartnerCommission) {
                            $PartnerCommission->status = 1;
                            $PartnerCommission->save();
                            $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                            if ($parent_api_key) {
                                $parent_api_key->balance += $PartnerCommission->profit;
                                $parent_api_key->save();

                                $Log = new Log();
                                $Log->date_time = $PartnerCommission->created_at;
                                $Log->final_amount = $PartnerCommission->profit;
                                $Log->balance = $parent_api_key->balance;
                                $Log->transection_type = 5;
                                $Log->transection_id = $PartnerCommission->id;
                                $Log->partner_id = $PartnerCommission->from_id;
                                $Log->source = 'Iframe-2';
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
                                    $summary_log->source = 'Iframe-2';
                                    $summary_log->save();
                                }
                            }
                        }

                        $commit = 1;
                        DB::commit();


                        if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($order->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $order->e_wallet_name,
                                "id" => strval($order->id),
                                'transaction_type' => 'Deposit',
                                "user_account_no" => strval($order->sender),

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
                                'user_account_no' => $order->sender,
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

                            if (!empty($order->member_id)) {
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

                    $processing = 1;
                    $message = "Please Wait! Your Payment is Processing.";
                    if ($commit == 0) {
                        DB::commit();
                    }
                    return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                }



                return view('partner.payout.paymentProcessingIframe2', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                } else {
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('processNextPayment-PartnerController Error: txn_id: ' . $txn_id . 'seccess ' . $success . ' Error: ' . $e->getMessage());
            }
        }


        return back()->with('error', $e->getMessage());
    }


    public function processNextPayment4(Request $request)
    {

        $username = $request->username;
        $ewallet = $request->ewallet;
        $amount = $request->amount;
        $fund_id = $request->fund_id;


        $Txn = Txn::where('txn_no', $request->txn)->orderBy('id', 'DESC')->first();
        if ($Txn) {
            $message = "This TXN no already used for another transection!";
            return back()->with('error', $message);
        }



        $api_key = API::where('username', $username)->where('status', 1)->where('type', 'Admin')->first();
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

        $fund = Payment::where('id', $fund_id)->latest()->first();
        $account = EWalletAccount::where('e_wallet_name', $fund->e_wallet_name)
            ->where('account_no', $fund->e_wallet_phone_number)
            ->where('status', 1)
            ->first();


        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
        if ($commissions) {
            $charge = $commissions->deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            }
        }



        if ($fund) {

            $parentIds = ParentCommission::where('user_id', $api_key->id)
                ->pluck('parent_id')
                ->unique()
                ->values();
            foreach ($parentIds as  $parentId) {

                $parent_charge = 0;

                $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                if ($parent_commission) {
                    $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                } else {
                    $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                    }
                }

                if ($parent_charge > 0) {
                    $PartnerCommission = new PartnerCommission();
                    $PartnerCommission->api_id = $api_key->id;
                    $PartnerCommission->from_id = $parentId;
                    $PartnerCommission->type = 1;
                    $PartnerCommission->amount = $amount;
                    $PartnerCommission->charges = $charge;
                    $PartnerCommission->total_amount = $amount - $charge;
                    $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                    $profit_p = $parent_commission->deposit_percentage;
                    $profit = $profit_p * $amount / 100;
                    $PartnerCommission->profit = $profit;
                    $PartnerCommission->profit_p = $profit_p;
                    $PartnerCommission->transaction_id = $fund['id'];
                    $PartnerCommission->status = 0;
                    $PartnerCommission->save();
                }
            }
        } else {
            $message = "Your Transection Already Processed!";
            return back()->with('error', $message);
        }

        $maxAttempts = 5;
        $attempt = 0;
        $success = 0;

        $txn_id = "";
        if ($request->filled('txn')) {
            $txn_id = $request->txn;
        }





        while ($attempt < $maxAttempts && $success == 0) {
            LaravelLog::info('processNextPayment-PartnerController try(' . $attempt + 1 . ') txn_id: ' . $txn_id);
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
                    // return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime','url','txn_id'));
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
                    $payment_record = PendingPayment::where('txn_id', $request->txn)->where('status', 0)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                    if (!$payment_record) {
                        $processing = 1;
                        $message = "Please Wait! Your Payment is Processing.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                    } else {
                        $check_payment_txn = Payment::where('txn_id', $payment_record->txn_id)->first();
                        if ($check_payment_txn) {
                            DB::rollBack();
                            $processing = 2;
                            $message = "By This Txn no, Payment Already Completed.";
                            return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                        }
                    }



                    if ($order->status == "Complete") {
                        $processing = 2;
                        $message = "With This Transaction No. Payment Already Completed.";
                        DB::commit();
                        return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                    }




                    $order = Payment::where('id', $fund->id)->where('status', 'Pending')->lockForUpdate()->first();
                    if (!$order) {
                        DB::rollBack();
                        abort(404);
                    }




                    $open_user = API::where('id', $order->api_id)->where('status', 1)->lockForUpdate()->first();
                    if (!$open_user || $open_user->type != "Admin") {
                        DB::rollBack();
                        abort(404);
                    }

                    $commit = 0;
                    if ($order && $order->amount == $payment_record->amount) {
                        if ($order->status == "Complete") {
                            $processing = 2;
                            $message = "Your Payment is Already Verified!";
                            DB::rollBack();
                            return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
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
                            $Log->transection_id = $order->id;
                            $Log->partner_id = $partner_api_key->id;
                            $Log->source = 'Iframe-2';
                            $Log->save();
                        }


                        // if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address=="111.111.11.111" || $payment_record->mac_address=="222.222.22.222")) {
                        //     if(!empty($order->account_no)){
                        //         $payment_record->sender = $order->account_no;
                        //     }
                        // }


                        $order->status = 'Complete';
                        $order->trans_complete_date = Carbon::now();
                        $order->completed_source = 'Iframe-2';
                        $order->charge = $charge;

                        if (empty($order->sender) || $order->sender == 0) {
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


                        $order->save();

                        $payment_record->status = 1;
                        $payment_record->save();
                        $payment_record = null;
                        // $payment_record->delete();

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
                            $summary_log->source = 'Iframe-2';
                            $summary_log->save();
                        }

                        $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                        foreach ($PartnerCommissions as $PartnerCommission) {
                            $PartnerCommission->status = 1;
                            $PartnerCommission->save();
                            $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                            if ($parent_api_key) {
                                $parent_api_key->balance += $PartnerCommission->profit;
                                $parent_api_key->save();

                                $Log = new Log();
                                $Log->date_time = $PartnerCommission->created_at;
                                $Log->final_amount = $PartnerCommission->profit;
                                $Log->balance = $parent_api_key->balance;
                                $Log->transection_type = 5;
                                $Log->transection_id = $PartnerCommission->id;
                                $Log->partner_id = $PartnerCommission->from_id;
                                $Log->source = 'Iframe-2';
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
                                    $summary_log->source = 'Iframe-2';
                                    $summary_log->save();
                                }
                            }
                        }

                        $commit = 1;
                        DB::commit();


                        if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                            $string_to_hash = json_encode(array(
                                "amount" => strval($this->convertStringToNumber($order->amount)),
                                "api_key" => $partner_api_key->api_key,
                                "e_wallet_name" => $order->e_wallet_name,
                                "id" => strval($order->id),
                                'transaction_type' => 'Deposit',
                                "user_account_no" => strval($order->sender),

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
                                'user_account_no' => $order->sender,
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

                            if (!empty($order->member_id)) {
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

                        $processing = 2;
                        $message = "Your transaction has been successfully completed";
                        return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                    }

                    $processing = 1;
                    $message = "Please Wait! Your Payment is Processing.";
                    if ($commit == 0) {
                        DB::commit();
                    }
                    return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
                }



                return view('partner.payout.paymentProcessingIframe4', compact('order', 'processing', 'id', 'logo', 'banner', 'ewallet', 'message', 'remainingTime', 'url', 'txn_id'));
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                } else {
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('processNextPayment-PartnerController Error: txn_id: ' . $txn_id . 'seccess ' . $success . ' Error: ' . $e->getMessage());
            }
        }


        return back()->with('error', $e->getMessage());
    }

    public function getaccount(Request $request)
    {

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
            'username' => $username,
            'ewallet' => $ewallet,
            'acc' => $acc,
            'amount' => $amount,
            'transection_id' => $transection_id,
            'member_id' => $member_id,
            'gate_id' => $gate_id,
        ];

        $jsaon =  json_encode($logarray);
        LaravelLog::info('getaccount:' . $jsaon);



        $current_time = Carbon::now('Asia/Dhaka');
        $Setting = Setting::where('name', 'last_account_active')->first();

        $now = Carbon::now();
        $startOfToday = $now->copy()->startOfDay();
        $startOfMonth = $now->copy()->startOfMonth();
        $oneMinuteAgo = $now->copy()->subMinute();

        // Query
        $results = Payment::selectRaw('
               e_wallet_phone_number,
               COUNT(CASE WHEN created_at >= ? THEN 1 END) AS counts_for_round_robin,
               COUNT(CASE WHEN trans_complete_date >= ? AND status = "Complete" THEN 1 END) AS today_count,
               COUNT(CASE WHEN trans_complete_date >= ? AND status = "Complete" THEN 1 END) AS month_count,
               COUNT(CASE WHEN created_at >= ? THEN 1 END) AS one_min_count,
                SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) AS one_min_sum
           ', [
            $Setting->value,
            $startOfToday,
            $startOfMonth,
            $oneMinuteAgo,
            $oneMinuteAgo
        ])
            ->where('gateway_id', $gate_id)
            ->whereNotNull('e_wallet_phone_number')
            ->where('e_wallet_phone_number', '!=', '')
            ->groupBy('e_wallet_phone_number')
            ->get();


        // ->where('created_at', '>=', $startOfMonth)

        $all_accounts = [];

        foreach ($results as $result) {
            $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
            $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
            $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
            $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
            $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
        }

        $account = EWalletAccount::where('e_wallet_name', $ewallet)
            ->where('monthly_limit', '>', 'monthly_received')
            ->whereRaw('daily_limit - daily_received > ?', [$amount])
            ->where('status', 1)
            ->whereIn('account_type', ['Deposit', 'Both'])
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
            $message = "You Can not Proceed With this E-wallet account";
            return response()->json(['status' => 'fail', 'message' => $message]);
        }


        $gate = Gateway::where('id', $gate_id)->first();

        $api_key = API::where('username', $username)->where('status', 1)->where('type', 'Admin')->first();
        if ($api_key) {
            $secretKey = $api_key->secret_key;
        } else {
            $message = "Wrong API key.";
            return response()->json(['status' => 'fail', 'message' => $message]);
        }
        $api_id = $api_key->id;

        $currentMonth = now()->format('Y-m');
        $sum = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');

        $charge = 0;


        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
        if ($commissions) {
            $charge = $commissions->deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
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
            if ($fund) {
                if ($fund->status != 2) {
                    $message = "Your Transection Already Processed!";
                    return response()->json(['status' => 'fail', 'message' => $message]);
                }
            }
        } else {
            $fund = Payment::where('gateway_id', $gate->id)->where('amount', $amount)->where('status', 'Pending')->where('sender', $acc)->where('api_id', $api_key->id)->where('created_at', '>=', $twoHoursAgo)->latest()->first();
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
            $fund->transaction = strRandom();
            $fund->try = 0;
            $fund->status = 'Pending';
            $fund->api_id = $api_key->id;
            $fund->e_wallet_phone_number = $e_wallet_phone_number;
            $fund->request_source = "Iframe-2";
            $fund->e_wallet_name = $gate->name;
            $fund->save();
        }
        return response()->json(['status' => 'success', 'phone_number' => $account->account_no, 'account_type' => $account->type, 'fund_id' => $fund->id]);
    }

    public function convertStringToNumber($string)
    {
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
        LaravelLog::info('processTransection3:' . $data_jsaon);

        $message = "";
        $txn_verification = "";
        $ewalletee = strtolower($ewallet);

        if ($ewalletee != "bkash") {
            return response()->json(['message' => 'You are only allowed to proceed with Bkash E-Wallet'], 404);
        }

        $gate = Gateway::where('code', $ewallet)->where('status', 1)->where('deposit_on' , 1)->first();
        if (!$gate) {
            $message = "Gateway is inactive.";
            return response()->json(['message' => $message], 404);
        }

        $api_key = API::where('username', $username)->where('status', 1)->select('id', 'type', 'secret_key', 'txn_verification', 'redirect_url', 'sign', 'api_key', 'min_deposit', 'parent_id')->first();
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




        if ($api_key->min_deposit > $amount) {
            $message = "Minimum Deposit Limit is " . $api_key->min_deposit;
            return response()->json(['message' => $message], 404);
        }


        if ($gate->max_amount < $amount) {
            $message = "Maximum Deposit Limit is " . round($gate->max_amount, 2);
            return response()->json(['message' => $message], 404);
        }


        $sum = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');

        $charge = 0;

        $e_wallet_name = "bKash";
        $acc_type = "Merchant";

        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$e_wallet_name}%")->where('type', 'like', "%{$acc_type}%")->first();
        if ($commissions) {
            $charge = $commissions->deposit_percentage * $amount / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$e_wallet_name}%")->where('type', 'like', "%{$acc_type}%")->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $amount / 100;
            }
        }


        $reqAmount = $amount;
        $payable = getAmount($reqAmount - $charge);
        $final_amo = getAmount($payable * $gate->convention_rate);


        $fund = Payment::where('partner_transection_id', $transection_id)->where('api_id', $api_key->id)->latest()->first();
        if ($fund) {
            $message = "This partner transaction ID has already been created!";
            return response()->json(['status' => 'fail', 'message' => $message]);
        }


        if (!empty($transection_id) || $transection_id != "0") {

            $merchantinvoice = $transection_id;
        } else {

            $merchantinvoice = "Invoice" . time();
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
        $fund->status = "Pending";
        $fund->api_id = $api_key->id;
        $fund->e_wallet_phone_number = $e_wallet_phone_number;
        $fund->request_source = "Iframe-3";
        $fund->e_wallet_name = $gate->name;
        $fund->sender = '';


        $accessToken = $this->getBkashToken($account);
        $createBkashPayment = $this->createBkashPayment($accessToken, $amount, $merchantinvoice, $account);


        if (isset($createBkashPayment['paymentID'])) {
            $fund->live_payment_id = $createBkashPayment['paymentID'];
            $fund->save();


            if($commissions){
                $parentIds = ParentCommission::where('user_id', $api_key->id)
                    ->pluck('parent_id')
                    ->unique()
                    ->values();
                foreach ($parentIds as  $parentId) {

                    $parent_charge = 0;

                    $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                    if ($parent_commission) {
                        $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                    } else {
                        $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                        if ($parent_commission) {
                            $parent_charge = $parent_commission->deposit_percentage * $amount / 100;
                        }
                    }

                    if ($parent_charge > 0) {
                        $PartnerCommission = new PartnerCommission();
                        $PartnerCommission->api_id = $api_key->id;
                        $PartnerCommission->from_id = $parentId;
                        $PartnerCommission->type = 1;
                        $PartnerCommission->amount = $amount;
                        $PartnerCommission->charges = $charge;
                        $PartnerCommission->total_amount = $amount - $charge;
                        $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                        $profit_p = $parent_commission->deposit_percentage;
                        $profit = $profit_p * $amount / 100;
                        $PartnerCommission->profit = $profit;
                        $PartnerCommission->profit_p = $profit_p;
                        $PartnerCommission->transaction_id = $fund['id'];
                        $PartnerCommission->status = 0;
                        $PartnerCommission->save();
                    }
                }
            }




            return redirect()->away($createBkashPayment['bkashURL']);
        } else {
            return response()->json(['message' => 'Gateway Error.'], 404);
        }
        exit;
    }

    public function getBkashToken($account)
    {
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

    public function createBkashPayment($accessToken, $amount, $merchantinvoice, $account)
    {
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



    public function bkashcallback(Request $request){

        $data = $request->all();
        $redirect_url = "https://ecpay.asia/index.html";
        $paymentID = $data['paymentID'];

        $fund = Payment::where('live_payment_id', $paymentID)->latest()->first();
        if($fund){

            $partner_api_key = Api::where('id', $fund->api_id)->first();
            $source = $partner_api_key->website;
            $api_id = $partner_api_key->id;

            if(!empty($partner_api_key->redirect_url)){
                $redirect_url = $partner_api_key->redirect_url;
            }

            if (empty($source)) {
                $source = "";
            }


            if ($fund->status == "Complete") {
                return redirect()->away($redirect_url);
            }

            $account = MerchantAccount::where('status', 1)
            ->where('e_wallet_phone_number', $fund->e_wallet_phone_number)
            ->first();

            if($account){
                $accessToken = $this->getBkashToken($account);
                $executeBkashPayment = $this->executeBkashPayment($accessToken, $paymentID, $account);
                if(isset($executeBkashPayment['paymentID'])){



                    $trxID = $executeBkashPayment['trxID'] ?? "";
                    $transactionStatus = $executeBkashPayment['transactionStatus'] ?? "";
                    $serviceFee = $executeBkashPayment['serviceFee'] ?? 0;
                    $account_no = $executeBkashPayment['payerAccount'] ?? "";

                    if($transactionStatus=="Completed"){
                        $now = Carbon::now();
                        $twoHoursAgo = $now->subHours(2);

                        DB::beginTransaction();
                        $payment = Payment::where('id', $fund->id)->orderBy('id', 'DESC')->lockForUpdate()->first();
                        $formattedDateTime = Carbon::now()->format('Y-m-d H:i:s');
                        $payment->date_time = $formattedDateTime;
                        $payment->e_wallet_name = "bkash";
                        $payment->txn_id = $trxID;
                        $payment->transaction_type = 'Received Money';
                        $payment->e_wallet_type = "Merchant";
                        $payment->api_id = $api_id;
                        $payment->e_wallet_charges = $serviceFee;
                        $payment->sender = $account_no;
                        $payment->status = 'Complete';
                        $payment->completed_source = 'I-frame';
                        $payment->trans_complete_date = Carbon::now();
                        $payment->save();






                        $commit = 0;
                        if ($fund) {


                            if ($source != env('APP_WEBSITE')) {


                                $charge = $fund->charge;

                                $partner_api_key_to_update = Api::where('id', $fund->api_id)->lockForUpdate()->first();
                                $net_amount = $payment->amount - $charge;
                                $partner_api_key_to_update->balance += $net_amount;
                                $partner_api_key_to_update->save();

                                $Log = new Log();
                                $Log->date_time = $payment->updated_at;
                                $Log->final_amount = $net_amount;
                                $Log->balance = $partner_api_key_to_update->balance;
                                $Log->transection_type = 1;
                                $Log->transection_id = $payment->id;
                                $Log->partner_id = $partner_api_key_to_update->id;
                                $Log->source = 'Iframe';
                                $Log->save();
                            }



                            $fund->status = "Complete";
                            $fund->save();

                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $fund->api_id)->whereDate('created_at', '>=', $fund->created_at)->get();
                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                                $amount_to_update = round($amount_to_update, 2);
                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                $DailyPartnerSummary_record->save();

                                $summary_log = new DailyPartnerSummaryLog();
                                $summary_log->partner_id = $partner_api_key_to_update->id;
                                $summary_log->partner_balance = $partner_api_key_to_update->balance;
                                $summary_log->payment_id = $payment->id;
                                $summary_log->total_amount = $net_amount;
                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                $summary_log->source = 'Iframe';
                                $summary_log->save();
                            }

                            $PartnerCommissions = PartnerCommission::where('transaction_id', $fund->id)->where('type', 1)->where('status', 0)->get();
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
                    }else{

                        $fund->status = 3;
                        $fund->save();

                    }

                }
            }





        }



        return redirect()->away($redirect_url);

    }


    public function executeBkashPayment($accessToken, $paymentID, $account)
    {

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


    public function queryBkashPayment($accessToken, $paymentID)
    {
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

    public function request()
    {
        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;

        $log = "View Withdrawal Requests";
        $this->addLogs($log);


        $sum = Payout::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');

        $api_key = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();

        $charge = 0;
        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->withdrawal_percentage * $user->balance / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->withdrawal_percentage * $user->balance / 100;
            }
        }

        $withdrawal_able_amount = $api_key->balance - $charge;
        $withdrawal_able_amount = round($withdrawal_able_amount ?? 0, 2);

        $pageTitle = __('partner_basic.payout_request_page_title');
        $domains = Api::where('type', 'Admin')->where('status', 1)->get();
        $records = Payout::where('transfer_status', 1)
            ->orderBy('id', 'DESC')
            ->with('gateway')
            ->when($api_id, function ($query) use ($api_id) {
                $query->where('api_id', $api_id);
            })
            ->paginate(config('basic.paginate'));
        return view('partner.payout.logs', compact('records', 'pageTitle', 'domains', 'withdrawal_able_amount'));
    }

    public  function action(Request $request, $id)
    {
        $this->validate($request, [
            'id' => 'required',
            'status' => ['required', Rule::in(['2', '3'])],
        ]);

        $data = Payout::where('id', $request->id)->where('transfer_status', 1)->firstOrFail();
        $api_key = Auth::guard('partner')->user();
        $partner = Api::where('api_key', $api_key->api_key)->where('type', 'Admin')->first();

        $previous_pending = Payout::where('api_id', $partner->id)
            ->where(function ($query) {
                $query->where('transfer_status', 1)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('transfer_status', 2)
                            ->where('status', 'Pending');
                    });
            })
            ->sum('amount');

        if ($previous_pending > $partner->balance) {
            if ($previous_pending > 0) {
                session()->flash('error', 'You have already requested a withdrawal of ' . round($previous_pending, 2) . ', which is still in process. Your remaining balance is ' . round($partner->balance - $previous_pending, 2) . '.');
                return back();
            } else {
                session()->flash('error', 'Insufficient balance' . snake2Title(round($partner->balance, 2)) . ' For Withdraw.');
                return back();
            }
        }

        $basic = (object) config('basic');


        if ($request->status == '2') {

            if (strtolower($data->e_wallet_name) == "nagad" || strtolower($data->e_wallet_name) == "rocket" || strtolower($data->e_wallet_name) == "bkash") {


                $log = "Approve Withdrawal Requests of " . $data->e_wallet_name . " Acc No:" . $data->user_account_no . " Amount:" . $data->amount;
                $this->addLogs($log);

                //  $result = $this->checkPayoutAmountWithinTime($data);

                $this->updateLimits();
                $this->updateEWallets();

                $current_time = Carbon::now('Asia/Dhaka');

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
                        COUNT(CASE WHEN created_at >= ? THEN 1 END) AS one_min_count,
                        SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) AS one_min_sum
                    ', [
                    $Setting->value,
                    $startOfToday,
                    $startOfMonth,
                    $oneMinuteAgo,
                    $oneMinuteAgo
                ])
                    ->where('e_wallet_name', $data->e_wallet_name)
                    ->whereNotNull('e_wallet_phone_number')
                    ->where('e_wallet_phone_number', '!=', '')
                    ->groupBy('e_wallet_phone_number')
                    ->get();


                // ->where('created_at', '>=', $startOfMonth)

                $all_accounts = [];

                foreach ($results as $result) {
                    $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
                    $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
                    $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
                    $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
                    $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
                }

                $account = EWalletAccount::where('e_wallet_name', $data->e_wallet_name)
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




                // $pre_payout = Payout::where('payout_log_id', $data->id)->first();
                // if (!$pre_payout) {
                //     $pre_payout = new Payout();
                // }

                if (isset($data->information->PhoneNumber->field_name)) {
                    $user_account_no =  $data->information->PhoneNumber->field_name;
                } else {
                    $user_account_no =  $data->user_account_no;
                }




                // $pre_payout->payout_log_id = $data->id;
                // $pre_payout->api_id = $data->api_id;
                // $pre_payout->e_wallet_name = $data->method->name;
                // $pre_payout->amount = $data->amount;
                $data->user_account_no = $user_account_no;
                $data->e_wallet_phone_number = $account->account_no;
                $data->e_wallet_type = $account->type;
                $data->status = 'Pending';
                // $data->payout_id = $pre_payout->id;
                $data->feedback = $request->feedback;




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

                $data->charge = $charge;
                $data->transfer_status = 2;
                $data->save();

                DB::commit();

                session()->flash('success', 'Payout Request has been sent');
                return back();
            }

            $data->transfer_status = 2;
            $data->feedback = $request->feedback;

            // dd($data);
            $data->save();

            session()->flash('success', 'Approve Successfully');
            return back();
        } elseif ($request->status == '3') {

            $log = "Reject Withdrawal Requests of " . $data->e_wallet_name . " Acc No:" . $data->user_account_no . " Amount:" . $data->amount;
            $this->addLogs($log);

            $data->transfer_status = 3;
            $data->feedback = $request->feedback;
            $data->status = 'Reject';

            $data->save();

            session()->flash('success', 'Reject Successfully');
            return back();
        }
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

    public function search(Request $request)
    {
        $log = "Search Withdrawal Requests";
        $this->addLogs($log);



        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;

        $search = $request->all();
        $domains = Api::where('type', 'Admin')->get();
        $dateSearch = $request->date_time;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);

        $from_date_to_search = date('Y-m-d H:i:s', strtotime($dateSearch . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($dateSearch . ' 23:59:59'));


        $partnerTimezone = $main_user->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $records = Payout::where('transfer_status', '!=', 0)
            ->when($search['date_time'], function ($query) use ($search, $from_date_to_search, $to_date_to_search) {
                $query->where('created_at', '>=', $from_date_to_search)
                    ->where('created_at', '<=', $to_date_to_search);
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
            ->when($api_id, function ($query) use ($api_id) {
                $query->where('api_id', $api_id);
            })
            ->orderBy('id', 'DESC')
            ->with('user', 'gateway')
            ->paginate(config('basic.paginate'));

        // dd($records);

        $sum = Payout::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('api_id', $api_id)
            ->where('status', 'Complete')
            ->sum('amount');
        $api_key = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $charge = 0;
        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->withdrawal_percentage * $user->balance / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->withdrawal_percentage * $user->balance / 100;
            }
        }
        $withdrawal_able_amount = $api_key->balance - $charge;
        $withdrawal_able_amount = round($withdrawal_able_amount ?? 0, 2);

        $pageTitle = "Search Payout Logs";
        return view('partner.payout.logs', compact('records', 'pageTitle', 'domains', 'withdrawal_able_amount'));
    }

    public function report()
    {
        $log = "View Withdrawal Report";
        $this->addLogs($log);

        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;

        $from_date = date('Y-m-d 00:00:00');
        $to_date = date('Y-m-d H:i:s');


        $partnerTimezone = $main_user->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $search['from_date'] = Carbon::parse($from_date, $originalTimezone)->setTimezone($targetTimezone);
        $search['to_date'] = Carbon::parse($to_date, $originalTimezone)->setTimezone($targetTimezone);

        $fromDate = Carbon::parse($search['from_date']);
        $toDate = Carbon::parse($search['to_date'])->setSecond(59);

        $gateways = Gateway::where('status', 1)
            ->get();
        // dd($gateways);
        $pageTitle = __('partner_basic.payout_request_page_title');
        $domains = Api::where('type', 'Admin')->get();
        $records = Payout::where('transfer_status', '!=', 0)
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)
            ->orderBy('id', 'DESC')
            ->with('user', 'gateway')
            ->when($api_id, function ($query) use ($api_id) {
                $query->where('api_id', $api_id);
            })
            ->paginate(config('basic.paginate'));

        $funds_t = Payout::where('transfer_status', '!=', 0)
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)
            ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
            ->when($api_id, function ($query) use ($api_id) {
                $query->where('api_id', $api_id);
            })
            ->first();
        $fund_count = $funds_t->fund_count;
        $fund_sum = round($funds_t->fund_sum, 2);
        return view('partner.payout.report', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum', 'from_date', 'to_date'));
    }

    public function reportSearch(Request $request)
    {
        $log = "Search Withdrawal Report";
        $this->addLogs($log);
        // dd($request->all());

        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;

        $search = $request->all();
        $domains = Api::where('type', 'Admin')->get();
        $gateways = Gateway::where('status', 1)->get();

        $fund_count = 0;
        $fund_sum = 0;


        $from_date = $search['from_date'];
        $to_date = $search['to_date'];



        $partnerTimezone = $main_user->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $search['from_date'] = Carbon::parse($search['from_date'], $originalTimezone)->setTimezone($targetTimezone);
        $search['to_date'] = Carbon::parse($search['to_date'], $originalTimezone)->setTimezone($targetTimezone);



        if (isset($search['export'])) {
            $records = Payout::when(isset($search['status']), function ($query) use ($search) {
                return $query->where('transfer_status', $search['status']);
            })
                ->when($api_id, function ($query) use ($api_id) {
                    $query->where('api_id', $api_id);
                })
                ->where('transfer_status', '!=', 0)
                ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                    $fromDate = Carbon::parse($search['from_date']);
                    $toDate = Carbon::parse($search['to_date'])->setSecond(59);
                    return $query->where('created_at', '>=', $fromDate)
                        ->where('created_at', '<=', $toDate);
                })
                ->when(isset($search['partner_transection_id']), function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('trx_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when(isset($search['account_no']), function ($query) use ($search) {
                    return $query->where('user_account_no', 'LIKE', "%{$search['account_no']}%");
                })
                ->when(isset($search['gateway']), function ($query) use ($search) {
                    return $query->where('e_wallet_name', 'LIKE', "%{$search['gateway']}%");
                })
                ->orderBy('id', 'DESC')
                ->with('gateway')
                ->get();

            $data[] = ['Date', 'System Generated Txn', 'E-Wallet Txn', 'Partner Txn', 'Username', 'User-Type', 'gateway', 'User-Account-No', 'Amount', 'Charges', 'Final-Amount', 'Request-Status', 'Transfer-Status', 'E-Wallet-No', 'Website', 'Completed-At'];
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

            $funds_t = Payout::when(isset($search['status']), function ($query) use ($search) {
                return $query->where('transfer_status', $search['status']);
            })
                ->when($api_id, function ($query) use ($api_id) {
                    $query->where('api_id', $api_id);
                })
                ->where('transfer_status', '!=', 0)
                ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                    $fromDate = Carbon::parse($search['from_date']);
                    $toDate = Carbon::parse($search['to_date'])->setSecond(59);
                    return $query->where('created_at', '>=', $fromDate)
                        ->where('created_at', '<=', $toDate);
                })
                ->when(isset($search['partner_transection_id']), function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('trx_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when(isset($search['account_no']), function ($query) use ($search) {
                    return $query->where('user_account_no', 'LIKE', "%{$search['account_no']}%");
                })
                ->when(isset($search['gateway']), function ($query) use ($search) {
                    return $query->where('e_wallet_name', 'LIKE', "%{$search['gateway']}%");
                })
                ->selectRaw('COUNT(*) as amount_count, SUM(amount) as amount_sum')
                ->with('gateway')
                ->first();


            if (!empty($funds_t) && isset($funds_t[0]->amount_count)) {
                $fund_count = $funds_t[0]->amount_count;
                $fund_sum = round($funds_t[0]->amount_sum, 2);
            }


            $records = Payout::when(isset($search['status']), function ($query) use ($search) {
                return $query->where('status', $search['status']);
            })
                ->when($api_id, function ($query) use ($api_id) {
                    $query->where('api_id', $api_id);
                })
                ->where('transfer_status', '!=', 0)
                ->when(isset($search['from_date']) && isset($search['to_date']), function ($query) use ($search) {
                    $fromDate = Carbon::parse($search['from_date']);
                    $toDate = Carbon::parse($search['to_date'])->setSecond(59);
                    return $query->where('created_at', '>=', $fromDate)
                        ->where('created_at', '<=', $toDate);
                })
                ->when(isset($search['partner_transection_id']), function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('trx_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('txn_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('partner_transection_id', 'like', '%' . $search['partner_transection_id'] . '%')
                            ->orWhere('member_id', 'like', '%' . $search['partner_transection_id'] . '%');
                    });
                })
                ->when(isset($search['account_no']), function ($query) use ($search) {
                    return $query->where('user_account_no', 'LIKE', "%{$search['account_no']}%");
                })
                ->when(isset($search['gateway']), function ($query) use ($search) {
                    return $query->where('e_wallet_name', 'LIKE', "%{$search['gateway']}%");
                })
                ->orderBy('id', 'DESC')
                ->with('gateway')
                ->paginate(config('basic.paginate'));

            $pageTitle = __('partner_basic.payout_request_page_title');
            return view('partner.payout.report', compact('records', 'pageTitle', 'domains', 'gateways', 'fund_count', 'fund_sum', 'from_date', 'to_date'));
        }
    }

    public function dailyReport()
    {
        $log = "View Day Wise Withdrawal Report";
        $this->addLogs($log);

        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;


        $from_date = date('Y-m-01');
        $to_date = date('Y-m-d');

        $from_date_to_search = date('Y-m-01 00:00:00');
        $to_date_to_search = date('Y-m-d 23:59:59');


        $partnerTimezone = $main_user->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');


        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Withdrawal Report";
        $payoutsByDate = Payout::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as payout_date"),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as complete_amount')
        )
            ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
            ->where('api_id', $api_id)
            ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"))
            ->get();
        // dd($payoutsByDate);

        return view('partner.payout.daily_report', compact('payoutsByDate', 'pageTitle', 'gateways', 'from_date', 'to_date'));
    }

    public function dailyReportSearch(Request $request)
    {
        $log = "Search Day Wise Withdrawal Report";
        $this->addLogs($log);

        $user = Auth::guard('partner')->user();
        $website = $user->website;
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;



        $from_date_to_search = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));


        $partnerTimezone = $main_user->timezone;
        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');


        $gateways = Gateway::where('status', 1)
            ->get();
        $pageTitle = "Daily Withdrawal Report";
        $query = Payout::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as payout_date"),
            DB::raw('COUNT(*) as payout_count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "Complete" THEN 1 END) as complete_count'),
            DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount'),
            DB::raw('SUM(CASE WHEN status = "Complete" THEN amount ELSE 0 END) as complete_amount')
        )
            ->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
            ->where('api_id', $api_id)
            ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"));


        if ($request->filled('gateway')) {
            $query->where('e_wallet_name', $request->gateway);
        }

        $payoutsByDate = $query->get();

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        return view('partner.payout.daily_report', compact('payoutsByDate', 'pageTitle', 'gateways', 'from_date', 'to_date'));
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

        $from_date_to_search = date('Y-m-d H:i:s', strtotime($date . ' 00:00:00'));
        $to_date_to_search = date('Y-m-d H:i:s', strtotime($date . ' 23:59:59'));


        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

        $records = Payout::where('transfer_status', 'like', '%' . $status . '%')
            ->where('transfer_status', '!=', 0)
            ->orderBy('id', 'DESC')
            ->with('gateway')
            ->when($api_id, function ($query) use ($api_id) {
                $query->where('api_id', $api_id);
            })
            ->where('created_at', '>=', $from_date_to_search)
            ->where('created_at', '<=', $to_date_to_search)
            ->where('e_wallet_name', 'like', '%' . $gateway . '%')
            ->get()
            ->map(function ($fund) use ($partnerTimezone) {
                $fund->created_at = \Carbon\Carbon::parse($fund->created_at)->timezone($partnerTimezone);
                $fund->updated_at = \Carbon\Carbon::parse($fund->updated_at)->timezone($partnerTimezone);
                return $fund;
            });

        // $funds_t = Payout::where('status', '!=', 0)
        //     ->selectRaw('COUNT(*) as fund_count, SUM(amount) as fund_sum')
        //     ->where('status', 'like', '%' . $status . '%')
        //     ->with('user', 'gateway')
        //     ->when($api_id, function ($query) use ($api_id) {
        //         $query->where('api_id', $api_id);
        //     })
        //     ->where('created_at', '>=', $from_date_to_search)
        //     ->where('created_at', '<=', $to_date_to_search)
        //     ->where('e_wallet_name', 'like', '%' . $gateway . '%')
        //     ->first();
        //     $fund_count = $funds_t->fund_count;
        //     $fund_sum = round($funds_t->fund_sum, 2);

        return response()->json($records);
    }





    public function payoutMoneyRequestTransection(Request $request)
    {
        $this->validate($request, [
            'gateway' => 'required|integer',
            'username' => 'required',
            'amount' => ['required', 'numeric']
        ]);

        $open_user = API::where('username', $request->username)->where('status', 1)->first();
        if (!$open_user || $open_user->type != "Admin") {
            abort(404);
        }

        $min_withdrawal = $open_user->min_withdrawal;


        $basic = (object)config('basic');
        $method = Gateway::where('id', $request->gateway)->where('status', 1)->where('withdrawal_on' ,1)->firstOrFail();



        $authWallet = $open_user;

        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);

        $finalAmo = $request->amount + $charge;

        if ($request->amount < $min_withdrawal) {
            session()->flash('error', 'Minimum payout Amount ' . round($min_withdrawal, 2) . ' ' . $basic->currency);
            return back();
        }
        if ($request->amount > $method->max_amount) {
            session()->flash('error', 'Maximum payout Amount ' . round($method->max_amount, 2) . ' ' . $basic->currency);
            return back();
        }



        $previous_pending = Payout::where('api_id', $open_user->id)
            ->where(function ($query) {
                $query->where('transfer_status', 1)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('transfer_status', 2)
                            ->where('status', 'Pending');
                    });
            })
            ->sum('amount');





        if ($finalAmo + $previous_pending > $authWallet['balance']) {
            if ($previous_pending > 0) {
                session()->flash('error', 'You have already requested a withdrawal of ' . round($previous_pending, 2) . ', which is still in process. Your remaining balance is ' . round($authWallet['balance'] - $previous_pending, 2) . '.');
                return back();
            } else {
                session()->flash('error', 'Insufficient balance' . snake2Title(round($authWallet['balance'], 2)) . ' For Withdraw.');
                return back();
            }
        } else {
            $trx = strRandom();
            $withdraw = new Payout();
            $withdraw->user_id = 0;
            $withdraw->gateway_id = $method->id;
            $withdraw->amount = getAmount($request->amount);
            $withdraw->charge = $charge;
            // $withdraw->net_amount = $finalAmo;
            $withdraw->trx_id = $trx;
            $withdraw->status = 0;
            $withdraw->e_wallet_name = $method->name;
            // $withdraw->api_key = $authWallet['api_key'];
            $withdraw->api_id = $authWallet['id'];
            $withdraw->save();
            session()->put('wtrx', $trx);
            session()->put('username', $request->username);
            return redirect()->route('partner.payout.preview.transection');
        }
    }

    public function newFundOpen(Request $request, $gate, $charge, $final_amo, $amount, $account_no, $open_user, $e_wallet_phone_number): Payment
    {

        $fund = new Payment();
        $fund->user_id = 0;
        $fund->e_wallet_name = $gate->name;
        $fund->gateway_id = $gate->id;
        $fund->amount = $amount;
        $fund->charge = $charge;
        $fund->sender = $account_no;
        $fund->transaction = strRandom();
        $fund->try = 0;
        $fund->status = "Pending";
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
            ->select(
                'api_id',
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
        $partners = Api::where('type', 'Admin')->where('status', 1)->get();
        return view('partner.payout.api', compact('records', 'pageTitle', 'partners'));
    }


    public function apiCommissions(Request $request)
    {
        $user = Auth::guard('partner')->user();

        $main_admin = Api::where('type', 'Admin')
            ->where('status', 1)
            ->where('api_key', $user->api_key)
            ->first();

        // Set default timezone if not found
        $partnerTimezone = $main_admin ? $main_admin->timezone : env('APP_TIMEZONE', 'Asia/Dhaka');

        if (!$main_admin) {
            // Log for debugging
            \Log::warning('Main admin API not found for partner.', [
                'partner_id' => $user->id,
                'api_key' => $user->api_key,
            ]);
            // Optional: show flash message or use toastr
            session()->flash('error', 'Admin configuration missing. Using default timezone.');
        }

        $partner_ids = PartnerCommission::where('from_id', $user->id)
            ->distinct()
            ->pluck('api_id')
            ->toArray();

        $partners = empty($partner_ids)
            ? collect()
            : Api::whereIn('id', $partner_ids)->get();

        $records = PartnerCommission::query();

        $from_date = $request->from_date ?: Carbon::today()->toDateString();
        $to_date = $request->to_date ?: Carbon::today()->toDateString();

        $from_date_to_search = Carbon::parse($from_date . ' 00:00:00', $partnerTimezone)
            ->setTimezone(env('APP_TIMEZONE', 'Asia/Dhaka'));
        $to_date_to_search = Carbon::parse($to_date . ' 23:59:59', $partnerTimezone)
            ->setTimezone(env('APP_TIMEZONE', 'Asia/Dhaka'));

        $records->where('created_at', '>=', $from_date_to_search);
        $records->where('created_at', '<=', $to_date_to_search);

        if (!empty($request->partner)) {
            $records->where('api_id', $request->partner);
        }

        $records->where('from_id', $user->id);

        if (!is_null($request->type) && $request->type !== '') {
            $records->where('type', $request->type);
        }

        $records->where('status', 1);

        $records = $records->orderBy('id', 'DESC')->paginate(config('basic.paginate'));

        $pageTitle = "Commission History";

        return view('partner.payout.commission_report', compact('records', 'pageTitle', 'partners', 'from_date', 'to_date'));
    }



    public function settlements()
    {
        $user = Auth::guard('partner')->user();

        if ($user->type !== "Admin") {
            return back()->with('error', 'You have no permission to this page.');
        }
        $now = now();
        // Calculate total settled amount for current month
        $sum = Settlement::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('partner_id', $user->id)
            ->where('status', '1')
            ->sum('amount');

        // Find admin API
        $api_key = Api::where([
            ['api_key', $user->api_key],
            ['type', 'Admin']
        ])->first();
        $charge = 0;
        $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->first();
        if ($commissions) {
            $charge = $commissions->settlement_percentage * $user->balance / 100;
        } else {
            $commissions = Commission::where('category_id', $api_key->category_id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->settlement_percentage * $user->balance / 100;
            }
        }
        $settlementableAmount = $user->balance - $charge;

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
            ->where('type', 'Admin')->where('status', 1)
            ->first();

        $charge = 0;
        if ($api) {
            $commission = Commission::where('category_id', $api->category_id)
                ->where('from_amount', '<=', $monthlyTotal)
                ->where('to_amount', '>=', $monthlyTotal)
                ->first();

            if (!$commission) {
                $commission = Commission::where('category_id', $api->category_id)
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




    public function dailyReportSettlement()
    {
        $this->addLogs("View Day Wise Settlement Report");

        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('status', 1)->where('api_key', $user->api_key)->first();
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
        $main_admin = Api::where('type', 'Admin')->where('status', 1)->where('api_key', $user->api_key)->first();
        $api_id = $main_admin->id;

        $records = ApiTransaction::where('partner_id', $api_id)->with('api')->orderBy('id', 'DESC')->get();
        $pageTitle = "Adjustments";
        $partners = Api::where('type', 'Admin')->where('status', 1)->get();

        return view('partner.payout.partner_balance', compact('records', 'pageTitle', 'partners'));
    }


      public function export_for_blance2(Request $request)
    {
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');
        $partner = $request->query('partner');
        $search_by_name = $request->query('search_by_name');
        $adjustment = $request->query('adjustment');
        try {
            // Use Carbon::parse() to handle various common date formats
            $carbonFrom = $from_date ? Carbon::parse($from_date) : null;
            $carbonTo = $to_date ? Carbon::parse($to_date) : null;

            $sanitizedDate = $carbonFrom ? $carbonFrom->format('Y-m-d') : 'no_date';
            $toDateFormatted = $carbonTo ? $carbonTo->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }

        return Excel::download(
            new PartnerBalanceExportForPartner($sanitizedDate, $toDateFormatted, $partner, $search_by_name, $adjustment),
            "partner_balance_by_date_{$sanitizedDate}.csv"
        );
    }


    public function partnerBalanceSearch(Request $request)
    {
        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('status', 1)->where('api_key', $user->api_key)->first();
        $partnerTimezone = $main_admin->timezone;
        $api_id = $main_admin->id;

        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');

        if (!empty($request->from_date)) {
            $from_date_to_search = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
            $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        }

        if (!empty($request->to_date)) {
            $to_date_to_search = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));
            $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
        }

        $partners = Api::where('type', 'Admin')->where('status', 1)->get();

        $records = ApiTransaction::with('api');

        // Date filters
        if (!empty($request->from_date) && !empty($request->to_date)) {
            $records->whereDate('created_at', '>=', $from_date_to_search);
            $records->whereDate('created_at', '<=', $to_date_to_search);
        } elseif (!empty($request->from_date)) {
            $records->whereDate('created_at', '>=', $from_date_to_search);
        } elseif (!empty($request->to_date)) {
            $records->whereDate('created_at', '<=', $to_date_to_search);
        }

        // Partner ID filter
        $records->where('partner_id', $api_id);

        // Adjustment filter
        if (!empty($request->adjustment) || $request->adjustment == '0') {
            $records->where('adjustment', $request->adjustment);
        }

        // Search by name, username, or website from related API model
        if (!empty($request->search_by_name)) {
            $searchTerm = $request->search_by_name;

            $records->where(function ($q) use ($searchTerm) {
                // Search in transaction fields
                $q->where('created_at', 'like', '%' . $searchTerm . '%')
                  ->orWhere('amount', 'like', '%' . $searchTerm . '%')
                  ->orWhere('charges', 'like', '%' . $searchTerm . '%')
                  ->orWhere('reason', 'like', '%' . $searchTerm . '%');

                // Search by adjustment type (label matching)
                if (stripos($searchTerm, 'deposit') !== false) {
                    $q->orWhere('adjustment', 2);
                } elseif (stripos($searchTerm, 'withdrawal') !== false) {
                    $q->orWhere('adjustment', 3);
                } elseif (stripos($searchTerm, 'top-up') !== false || stripos($searchTerm, 'topup') !== false) {
                    $q->orWhere('adjustment', 4);
                } elseif (stripos($searchTerm, 'balance') !== false) {
                    $q->orWhere('adjustment', 1);
                }
            });
        }


        $records = $records->orderBy('id', 'DESC')->get();

        $pageTitle = "Search Adjustments";
        return view('partner.payout.partner_balance', compact('records', 'pageTitle', 'partners'));
    }




    public function processMyPayment(Request $request)
    {
        session()->put('processing_show', 1);
        $track = session()->get('track');
        $sender = session()->get('sender');

        $username = session()->get('username');
        $open_user = API::where('username', $username)->where('status', 1)->first();

        if (!$open_user || $open_user->type != "Admin") {
            abort(404);
        }

        if ($open_user->txn_verification == 1) {
            $txn_verified = 0;
            if (session()->has('txn_verified')) {
                $txn_verified = session()->get('txn_verified');
            }
            if ($txn_verified == 0) {
                session()->put('processing_show', 2);
            }
        }

        $processing = session()->get('processing_show');
        $order = Payment::where('transaction', $track)->where('status', 2)->orderBy('id', 'DESC')->with(['gateway', 'user'])->first();
        $pageTitle = "Search Adjustments";
        return view('partner.payout.paymentProcessingOpen', compact('order', 'processing', 'pageTitle', 'username'));
    }

    public function verifytxn(Request $request)
    {

        $maxAttempts = 5;
        $attempt = 0;
        $success = 0;

        $txn_id = "";
        if ($request->filled('txn')) {
            $txn_id = $request->txn;
        }

        while ($attempt < $maxAttempts && $success == 0) {
            LaravelLog::info('verifytxn-PartnerController try(' . $attempt + 1 . ') txn_id: ' . $txn_id);

            DB::beginTransaction();
            try {
                $track = session()->get('track');
                $order = Payment::where('transaction', $track)->where('status', 'Pending')->orderBy('id', 'DESC')->lockForUpdate()->first();

                $username = session()->get('username');
                $api_key = API::where('username', $username)->where('status', 1)->lockForUpdate()->first();
                if (!$api_key || $api_key->type != "Admin") {
                    abort(404);
                }

                if (!$request->filled('txn') || empty($request->txn)) {
                    return back()->with('error', 'Kindly Fill Transaction Number.');
                }

                if ($api_key) {
                    $source = $api_key->website;
                    $api_id = $api_key->id;
                    if (empty($source)) {
                        $source = "";
                    }

                    $secretKey = $api_key->secret_key;
                } else {
                    return back()->with('error', 'Wrong API key.');
                }

                $now = Carbon::now();
                $twoHoursAgo = $now->subHours(2);

                $Txn = Txn::where('txn_no', $request->txn)->where('api_id', $api_id)->orderBy('id', 'DESC')->first();
                if (!$Txn) {
                    $Txn = new Txn();
                    $Txn->txn_no = $request->txn;
                    $Txn->api_id = $api_id;
                    $Txn->partner_transection_id = $order->partner_transection_id;
                    $Txn->save();
                }

                $payment_record = PendingPayment::where('txn_id', $request->txn)->where('status', 0)->where('created_at', '>=', $twoHoursAgo)->orderBy('id', 'DESC')->lockForUpdate()->first();
                if (!$payment_record) {
                    DB::rollBack();
                    session()->put('txn_verified', 1);
                    return back()->with('success', 'Please Wait! Your Payment is Processing.');
                } else {
                    $check_payment_txn = Payment::where('txn_id', $payment_record->txn_id)->first();
                    if ($check_payment_txn) {
                        DB::rollBack();
                        return back()->with('success', 'By This Txn no, Payment Already Completed.');
                    }
                }

                $charge = 0;
                $commit = 0;
                if ($order && $order->amount == $payment_record->amount) {
                    if ($order->status == "Cpmplete") {
                        return redirect()->route('partner.depositFund', ['username' => $username])->with('success', 'Your Payment is Already Verified!');
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

                        $net_amount = $payment_record->amount - $charge;
                        $partner_api_key->balance += $net_amount;
                        $partner_api_key->save();

                        $Log = new Log();
                        $Log->date_time = $payment_record->updated_at;
                        $Log->final_amount = $net_amount;
                        $Log->balance = $partner_api_key->balance;
                        $Log->transection_type = 1;
                        $Log->transection_id = $order->id;
                        $Log->partner_id = $partner_api_key->id;
                        $Log->source = 'VerifyByPartnerLink';
                        $Log->save();
                    }


                    if (strpos($payment_record->sender, 'XXXX') !== false && ($payment_record->mac_address == "111.111.11.111" || $payment_record->mac_address == "222.222.22.222")) {
                        if (!empty($order->account_no)) {
                            $payment_record->sender = $order->account_no;
                        }
                    }


                    $order->status = 'Complete';
                    $order->trans_complete_date = Carbon::now();
                    $order->completed_source = 'VerifyByPartnerLink';
                    $order->charge = $charge;
                    $order->sender = $payment_record->sender;

                    if (empty($order->sender) || $order->sender == 0) {
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


                    $order->save();
                    $payment_record->status = 1;
                    $payment_record->save();
                    $payment_record = null;
                    // $payment_record->delete();


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
                        $summary_log->source = 'VerifyByPartnerLink';
                        $summary_log->save();
                    }

                    $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                    foreach ($PartnerCommissions as $PartnerCommission) {
                        $PartnerCommission->status = 1;
                        $PartnerCommission->save();
                        $parent_api_key = Api::where('id', $PartnerCommission->from_id)->where('status', 1)->lockForUpdate()->first();
                        if ($parent_api_key) {
                            $parent_api_key->balance += $PartnerCommission->profit;
                            $parent_api_key->save();

                            $Log = new Log();
                            $Log->date_time = $PartnerCommission->created_at;
                            $Log->final_amount = $PartnerCommission->profit;
                            $Log->balance = $parent_api_key->balance;
                            $Log->transection_type = 5;
                            $Log->transection_id = $PartnerCommission->id;
                            $Log->partner_id = $PartnerCommission->from_id;
                            $Log->source = 'VerifyByPartnerLink';
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
                                $summary_log->source = 'VerifyByPartnerLink';
                                $summary_log->save();
                            }
                        }
                    }


                    $commit = 1;
                    DB::commit();

                    if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {

                        $string_to_hash = json_encode(array(
                            "amount" => strval($this->convertStringToNumber($order->amount)),
                            "api_key" => $partner_api_key->api_key,
                            "e_wallet_name" => $order->e_wallet_name,
                            "id" => strval($order->id),
                            'transaction_type' => 'Deposit',
                            "user_account_no" => strval($order->sender),

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
                            'user_account_no' => $order->sender,
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

                        if (!empty($order->member_id)) {
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

                if ($commit == 0) {
                    DB::commit();
                }
                session()->put('txn_verified', 1);
                return back()->with('success', 'Please Wait! Your Payment is Processing.');
            } catch (\Exception $e) {
                DB::rollBack();

                if (stripos($e->getMessage(), 'lock') !== false) {
                    $success = 0;
                    sleep(1);
                } else {
                    $success = 1;
                }

                $attempt++;

                LaravelLog::info('verifytxn-PartnerController Error: txn_id: ' . $txn_id . ' Error: ' . $e->getMessage());
            }
        }

        return response()->json(['error' => $e->getMessage()], 422);
    }

    public function update_order_fund_status(Request $request)
    {
        $track = session()->get('track');
        $order = Payment::where('transaction', $track)->where('status', 'Complete')->orderBy('id', 'DESC')->first();
        if ($order) {
            return json_encode(['status' => 'success']);
        } else {
            return json_encode(['status' => 'false']);
        }
    }

    public function payoutPreviewTransection()
    {

        $withdraw = Payout::latest()->where('trx_id', session()->get('wtrx'))->whereIn('transfer_status', [0, 1])->latest()->with('gateway', 'user')->firstOrFail();
        $title = "Payout Form";
        $username = session()->get('username');
        $open_user = API::where('username', $username)->where('status', 1)->first();

        if (!$open_user || $open_user->type != "Admin") {
            abort(404);
        }
        $remaining = getAmount($open_user->balance - $withdraw->net_amount);
        return view('partner.payout.previewopen', compact('withdraw', 'title', 'remaining'));
    }

    public function payoutRequestSubmitTransection(Request $request)
    {
        $basic = (object)config('basic');
        $withdraw = Payout::latest()->where('trx_id', session()->get('wtrx'))->whereIn('transfer_status', [0, 1])->with('gateway', 'user')->firstOrFail();
        $rules = [];
        $inputField = [];
        if (optional($withdraw->gateway)->input_form != null) {
            foreach ($withdraw->gateway->input_form as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);
        $username = session()->get('username');
        $open_user = API::where('username', $username)->where('status', 1)->first();
        $user = $open_user;

        $PhoneNumber = "";

        $previous_pending = Payout::where('api_id', $open_user->id)
            ->where(function ($query) {
                $query->where('transfer_status', 1)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('transfer_status', 2)
                            ->where('status', 'Pending');
                    });
            })
            ->sum('amount');

        if ($previous_pending > $user->balance) {
            if ($previous_pending > 0) {
                session()->flash('error', 'You have already requested a withdrawal of ' . round($previous_pending, 2) . ', which is still in process. Your remaining balance is ' . round($user->balance - $previous_pending, 2) . '.');
                return back();
            } else {
                session()->flash('error', 'Insufficient balance' . snake2Title(round($user->balance, 2)) . ' For Withdraw.');
                return back();
            }
        } else {

            $collection = collect($request);


            $reqField = [];
            if ($withdraw->gateway->input_form != null) {
                foreach ($collection as $k => $v) {
                    foreach ($withdraw->gateway->input_form as $inKey => $inVal) {
                        if ($k != $inKey) {
                            continue;
                        } else {

                            if ($inVal->type == 'file') {
                                if ($request->hasFile($inKey)) {
                                    $image = $request->file($inKey);
                                    $filename = time() . uniqid() . '.jpg';
                                    $location = config('location.withdrawLog.path');
                                    $reqField[$inKey] = [
                                        'field_name' => $filename,
                                        'type' => $inVal->type,
                                    ];
                                    try {
                                        $this->uploadImage($image, $location, $size = null, $old = null, $thumb = null, $filename);
                                    } catch (\Exception $exp) {
                                        return back()->with('error', 'Image could not be uploaded.');
                                    }
                                }
                            } else {
                                if ($inKey == "PhoneNumber") {
                                    $PhoneNumber = $v;
                                }
                                $reqField[$inKey] = $v;
                                $reqField[$inKey] = [
                                    'field_name' => $v,
                                    'type' => $inVal->type,
                                ];
                            }
                        }
                    }
                }
                $withdraw['information'] = $reqField;
            } else {
                $withdraw['information'] = null;
            }


            if (isset($collection['PhoneNumber'])) {
                $PhoneNumber = $collection['PhoneNumber'];
            }

            $method = Gateway::where('id', $withdraw->gateway_id)->where('status', 1)->where('withdrawal_on' ,1)->firstOrFail();

            $acc = $PhoneNumber;
            $ewalletee = strtolower($method->name);

            if (!is_numeric($acc)) {
                return back()->with('error', 'Account number formate not valid');
            }

            if (substr($acc, 0, 2) === "01") {
                $num_digits = strlen($acc);
                if ($ewalletee == 'bkash' && $num_digits != 11) {
                    return back()->with('error', 'Account number should be 11 digit');
                }
                if ($ewalletee == 'nagad' && $num_digits != 11) {
                    return back()->with('error', 'Account number should be 11 digit');
                }
                if ($ewalletee == 'rocket' && ($num_digits < 11 || $num_digits > 12)) {
                    return back()->with('error', 'Account number should be 11 or 12 digit');
                }
            } else {
                return back()->with('error', 'Account number should start from 01');
            }

            $source = $user->website;
            $api_id = $user->api_id;




            $withdraw->e_wallet_name = $method->name;
            $withdraw->amount = $withdraw->amount;
            $withdraw->user_account_no = $PhoneNumber;
            $withdraw->save();

            $charge = 0;



            $withdraw->transfer_status = 1;
            $withdraw->charge = $charge;
            $withdraw->save();

            session()->flash('success', 'Payout request Successfully Submitted. Wait For Confirmation.');
            return redirect()->route('partner.methods.get', ['username' => $username]);
        }
    }

    public function storeSettlement(Request $request)
    {
        $user = Auth::guard('partner')->user();

        if ($user->type != "Admin") {
            return response()->json([
                'status' => 'error',
                'message' => 'You have no permission to this page.'
            ], 403);
        }

        // Optional: validate request
        $validator = Validator::make($request->all(), [
            'source' => 'required|string|max:255',
            'source_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $sum = Settlement::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('partner_id', $user->id)
                ->where('status', '1')
                ->sum('amount');

            $api_key = Api::where('api_key', $user->api_key)
                ->where('type', 'Admin')
                ->first();

            $charge = 0;
            $commissions = Commission::where('category_id', $api_key->category_id)
                ->where('from_amount', '<=', $sum)
                ->where('to_amount', '>=', $sum)
                ->first();

            if ($commissions) {
                $charge = $commissions->settlement_percentage * $request->amount / 100;
            } else {
                $commissions = Commission::where('category_id', $api_key->category_id)
                    ->orderBy('to_amount', 'desc')
                    ->first();

                if ($commissions) {
                    $charge = $commissions->settlement_percentage * $request->amount / 100;
                }
            }

            if ($user->balance < $request->amount + $charge) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'You can only enter amount less than your transferable settlement balance.'
                ], 422);
            }

            $settlement = new Settlement();
            $settlement->source = $request->source;
            $settlement->source_name = $request->source_name;
            $settlement->account_no = $request->account_no;
            $settlement->amount = $request->amount;
            $settlement->charges = $charge;
            $settlement->net_amount = $request->amount + $charge;
            $settlement->partner_id = $user->id;
            $settlement->status = 0;
            $settlement->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settlement Saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Settlement Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the settlement. Please try again.'
            ], 500);
        }
    }
}
