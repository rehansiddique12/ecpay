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
use Illuminate\Support\Facades\Log as LaravelLog;
use App\Models\Commission;
use App\Models\DailyPartnerSummaryLog;
use App\Models\DailyPartnerSummary;
use App\ModelsPartnerCommission;

class PaymentLogController extends Controller
{
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
                    $order->completion_at = $order->created_at;
                    $order->trans_completed_date = Carbon::now();
                    $payment_record->completion_at = $order->created_at;
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
}
