<?php

namespace App\Http\Controllers\Partner;

use App\Models\Api;
use App\Models\Gateway;
use App\Models\PayoutMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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


}
