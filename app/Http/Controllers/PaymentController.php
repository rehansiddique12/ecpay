<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Api;
use App\Models\TransactionSetting;
use Carbon\Carbon;
use App\Models\EWalletAccount;
use App\Models\Commission;

class PaymentController extends Controller
{


    public function paymentGateway()
    {

        $gateways = Gateway::where('status', 1)
            ->select('name', 'image')
            ->get();

        foreach ($gateways as $key => $gateway) {
            $data[$key]['name'] = $gateway->name;
            $data[$key]['image'] = $gateway->image ? (env('APP_URL') . config('location.gateway.path') . $gateway->image) : '';
        }

        return $data;
    }

    public function paymentGatewayInfo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'e_wallet_name' => 'required|string',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $request->amount = str_replace(',', '', $request->amount);
        $user_sign = "";

        $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->first();
        if ($api_key) {
            $source = $api_key->website;
            $api_id = $api_key->id;
            $secretKey = $api_key->secret_key;

            $user_account_no = "";
            if($api_key->txn_verification==0){
                $acc = $request->user_account_no;
                $ewalletee = strtolower($request->e_wallet_name);

                if (!is_numeric($acc)) {
                    return response()->json(['code'=>605, 'error' => 'Account number formate not valid'], 404);
                }

                if (substr($acc, 0, 2) === "01") {
                    $num_digits = strlen($acc);
                    if ($ewalletee == 'bkash' && $num_digits!=11) {
                        return response()->json(['code'=>605, 'error' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'nagad' && $num_digits!=11) {
                        return response()->json(['code'=>605, 'error' => 'Account number should be 11 digit'], 404);
                    }
                    if ($ewalletee == 'rocket' && ($num_digits<11 || $num_digits>12)) {
                        return response()->json(['code'=>605, 'error' => 'Account number should be 11 or 12 digit'], 404);
                    }
                } else {
                    return response()->json(['code'=>605, 'error' => 'Account number should start from 01'], 404);
                }

                $user_account_no = $request->user_account_no;
            }elseif($request->filled('user_account_no')){
                $user_account_no = $request->user_account_no;
            }


            if($api_key->sign==1){
                if ($request->filled('sign')) {
                    if($api_key->txn_verification==0){
                        $string_to_hash = json_encode(array(
                            "amount" => $request->amount,
                            "api_key" => $request->api_key,
                            "e_wallet_name" => $request->e_wallet_name,
                            "user_account_no" => $user_account_no
                        ));
                    }else{
                        $string_to_hash = json_encode(array(
                            "amount" => $request->amount,
                            "api_key" => $request->api_key,
                            "e_wallet_name" => $request->e_wallet_name
                        ));
                    }

                    $user_sign = $request->sign;
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
                    if(hash_equals($request_hash, $hmac)){
                        if($sign_timestamp >= $timestamp-60 && $sign_timestamp <= $timestamp+60){
                            $signature = Signature::where('sign', $sign)->first();
                            if(!$signature){
                                $signature = new Signature();
                                $signature->sign = $sign;
                                $signature->save();
                            }else{
                                return response()->json(['code'=>601, 'message' => 'signature Already Used.'], 404);
                            }
                        }else{
                            return response()->json(['code'=>602, 'message' => 'signature Timeout'], 404);
                        }
                    }else{
                       return response()->json(['code'=>603, 'message' => 'Wrong Sign. Data may have been tampered with.'], 404);
                    }
                }else{
                   return response()->json(['code'=>604, 'message' => 'sign parameter should not be empty.'], 404);
                }
            }

        } else {
            return response()->json(['message' => 'Wrong API key'], 404);
        }

        if ($api_key->min_deposit > $request->amount) {
            return response()->json(['message' => 'Min Deposit Limit is ' . $api_key->min_deposit], 404);
        }

        $partner_transection_id = 0;

        if ($request->filled('partner_transection_id')) {
            $partner_transection_id = $request->partner_transection_id;
        }

        $member_id = "";
        if ($request->filled('member_id')) {
            $member_id = $request->member_id;
        }



        $setting = TransactionSetting::first();
        $fromTime = \DateTime::createFromFormat('H:i:s', $setting->from_time);
        $toTime = \DateTime::createFromFormat('H:i:s', $setting->to_time);
        $currentTime = now();

        $this->updateLimits();
        $this->updateEWallets();

        $current_time = Carbon::now('Asia/Dhaka');

            $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                ->where('type', 'Agent')
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
                    ->orderBy('daily_received', 'asc')
                ->first();
            if (!$account) {
                    $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                    ->where('type', 'Merchant')
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
                        ->orderBy('daily_received', 'asc')
                    ->first();
                if (!$account) {
                    $account = EWalletAccount::where('e_wallet_name', $request->e_wallet_name)
                        ->where('type', 'Personal')
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
                        ->orderBy('daily_received', 'asc')
                        ->first();

                }
            }

        if (!$account) {
            return response()->json(['error' => 'You Can not Proceed With this E-wallet account'], 422);
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
            $charge = $commissions->deposit_percentage * $request->amount / 100;
        } else {
            $commissions = Commission::where('api_id', $api_key->id)->orderBy('to_amount', 'desc')->first();
            if ($commissions) {
                $charge = $commissions->deposit_percentage * $request->amount / 100;
            }
        }

        $gate = Gateway::where('code', $request->e_wallet_name)->where('status', 1)->first();
        if(!$gate){
            return response()->json(['error' => 'Gateway Error. Contact to Administator!'], 422);
        }
        $reqAmount = $request->amount;
        $payable = getAmount($reqAmount - $charge);
        $final_amo = getAmount($payable * $gate->convention_rate);
        $e_wallet_phone_number = $account->account_no;

        $fund = Payment::where('partner_transection_id', $partner_transection_id)->where('api_id', $api_id)->latest()->first();
        if($fund){
            return response()->json(['error' => 'Duplicate Transection with Partner Transection Id not allowed!'], 422);
        }

        $fund = new Payment();
        $fund->user_id = 0;
        $fund->gateway_id = $gate->id;
        // $fund->gateway_currency = strtoupper($gate->currency);
        $fund->amount = $request->amount;
        $fund->partner_transection_id = $partner_transection_id;
        $fund->member_id = $member_id;
        $fund->charge = $charge;
        $fund->sender = $user_account_no;
        // $fund->rate = $gate->convention_rate;
        // $fund->final_amount = getAmount($final_amo);
        // $fund->btc_amount = 0;
        // $fund->btc_wallet = "";
        $fund->transaction = strRandom();
        $fund->try = 0;
        // $fund->sign = $user_sign;
        $fund->status = 2;
        // $fund->api_key = $request->api_key;
        $fund->api_id = $api_id;
        $fund->e_wallet_phone_number = $e_wallet_phone_number;
        $fund->request_source  = "API";
        $fund->save();


        if ($charge > 0 && $api_key->parent_id > 0) {
            // $parent_commissions = Commission::where('id', $commissions->parent_id)->first();
            if($commissions->parent_id>0 && $commissions->parent_deposit_percentage>0){
                $PartnerCommission = new PartnerCommission();
                $PartnerCommission->api_id = $api_key->id;
                $PartnerCommission->from_id = $api_key->parent_id;
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

            if($commissions->parent2_id>0 && $commissions->parent2_deposit_percentage>0){
                $PartnerCommission = new PartnerCommission();
                $PartnerCommission->api_id = $api_key->id;
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

        // env('APP_URL') . config('location.gateway.path')

        $data['name'] = $account->e_wallet_name;
        $data['phone_number'] = $account->account_no;
        $data['account_type'] = $account->type;
        $data['qr_image'] = $account->image ? (env('APP_URL') . config('location.withdraw.path') . $account->image) : '';

        return $data;
    }

    public function uploadReceipt(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'api_key' => 'required|string',
            'e_wallet_name' => 'required|string',
            'amount' => 'required',
            'user_account_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $request->amount = str_replace(',', '', $request->amount);

        $api_key = Api::where('api_key', $request->api_key)->where('type', 'Admin')->first();
        if ($api_key) {
            $source = $api_key->website;
                    $secretKey = $api_key->secret_key;

            if($api_key->sign==1){
                if ($request->filled('sign')) {
                    $string_to_hash = json_encode(array(
                        "amount" => $request->amount,
                        "api_key" => $request->api_key,
                        "e_wallet_name" => $request->e_wallet_name,
                        "user_account_no" => $request->user_account_no
                    ));
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
                    if(hash_equals($request_hash, $hmac)){
                        if($sign_timestamp >= $timestamp-60 && $sign_timestamp <= $timestamp+60){
                            $signature = Signature::where('sign', $sign)->first();
                            if(!$signature){
                                $signature = new Signature();
                                $signature->sign = $sign;
                                $signature->save();
                            }else{
                                return response()->json(['code'=>601, 'message' => 'signature Already Used.'], 404);
                            }
                        }else{
                            return response()->json(['code'=>602, 'message' => 'signature Timeout'], 404);
                        }
                    }else{
                       return response()->json(['code'=>603, 'message' => 'Wrong Sign. Data may have been tampered with.'], 404);
                    }
                }else{
                   return response()->json(['code'=>604, 'message' => 'sign parameter should not be empty.'], 404);
                }
            }
        } else {
            return response()->json(['message' => 'Wrong API key'], 404);
        }




        if ($request->hasFile('image')) {

            try {
                $uploadedImage = $this->uploadImage($request->image, config('location.receipts.path'));
                $gate = Gateway::where('code', $request->e_wallet_name)->where('status', 1)->first();
                $order = Payment::where('sender ', $request->user_account_no)->where('api_id', $api_key->id)->where('amount', $request->amount)->where('gateway_id', $gate->id)->latest()->first();
                if ($order) {
                    if (empty($order->receipt_image)) {
                        $order->receipt_image = $uploadedImage;
                        if ($order->payment_id > 0) {
                            if ($source != env('APP_WEBSITE')) {
                                // $partner_api_key = Api::where('id', $order->api_id)->first();
                                // $partner_api_key->balance = $partner_api_key->balance + $order->amount - $order->charge;
                                // $partner_api_key->save();
                                $payment = Payment::where('e_wallet_name', $request->e_wallet_name)
                                    ->where('amount', $request->amount)
                                    ->where('sender', $request->user_account_no)
                                    ->latest()->first();
                                //  $payment->status = 'Complete';
                                $payment->save();
                                //  $order->status = 1;
                            }
                        }
                        $order->save();
                        return response()->json(['message' => 'Image uploaded successfully']);
                    }
                }
                return response()->json(['message' => 'No records found.'], 404);
            } catch (\Exception $exp) {
                return response()->json(['error' => 'Image could not be uploaded.'], 500);
            }
        }
    }

    public function lastPaymentDetail(Request $request)
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

            if($api_key->sign==1){
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
                    if(hash_equals($request_hash, $hmac)){
                        if($sign_timestamp >= $timestamp-60 && $sign_timestamp <= $timestamp+60){
                            $signature = Signature::where('sign', $sign)->first();
                            if(!$signature){
                                $signature = new Signature();
                                $signature->sign = $sign;
                                $signature->save();
                            }else{
                                return response()->json(['code'=>601, 'message' => 'signature Already Used.'], 404);
                            }
                        }else{
                            return response()->json(['code'=>602, 'message' => 'signature Timeout'], 404);
                        }
                    }else{
                       return response()->json(['code'=>603, 'message' => 'Wrong Sign. Data may have been tampered with.'], 404);
                    }
                }else{
                   return response()->json(['code'=>604, 'message' => 'sign parameter should not be empty.'], 404);
                }
            }

        } else {
            return response()->json(['message' => 'Wrong API key'], 404);
        }

        if ($request->filled('partner_transection_id')) {
            $lastPayment = Payment::where('partner_transection_id', $request->partner_transection_id)->where('api_id', $api_key->id)
                ->latest()->first();
            if ($lastPayment) {
                $lastPayment->api_id = "";
                if (is_null($lastPayment->member_id)) {
                    unset($lastPayment->member_id);
                }
                return response()->json($lastPayment);
            } else {
                 $lastPayment = Payment::where('partner_transection_id', $request->partner_transection_id)->where('api_id', $api_key->id)->with(['gateway'])
                ->latest()->first();
                if($lastPayment){
                    $e_wallet_name = "";
                    $gate = Gateway::where('id', $lastPayment->gateway_id)->first();
                    if($gate){
                        $e_wallet_name = $gate->name;
                    }
                    if($lastPayment->status==1){
                        $status ="Complete";
                    }elseif($lastPayment->status==3){
                        $status ="Reject";
                    }else{
                        $status ="Pending";
                    }
                    $payment_response = [
                        'status'=>$status,
                        'e_wallet_name'=>$e_wallet_name,
                        'amount'=>$lastPayment->amount,
                        'sender'=>$lastPayment->account_no,
                        'created_at'=>$lastPayment->created_at,
                        'e_wallet_phone_number'=>$lastPayment->e_wallet_phone_number,
                        'charge'=>$lastPayment->charge,
                        'partner_transection_id'=>$lastPayment->partner_transection_id,
                    ];

                    if(!empty($lastPayment->member_id)){
                        $payment_response['member_id'] = $lastPayment->member_id;
                    }


                    return response()->json($payment_response);
                }
                return response()->json(['message' => 'No Payment records found.'], 404);
            }
        }



        $lastPayment = Payment::where('e_wallet_name', $request->e_wallet_name)->where('api_id', $api_key->id)
            ->where('amount', $request->amount)
            ->where('sender', $request->user_account_no)
            ->latest()->first();


        if ($lastPayment) {
            if (is_null($lastPayment->member_id)) {
                    unset($lastPayment->member_id);
                }
            return response()->json($lastPayment);
        } else {
            $lastPayment = Payment::where('amount', $request->amount)->where('api_id', $api_key->id)
            ->where('sender ', $request->user_account_no)
            ->whereHas('gateway', function ($query) use ($request) {
                $query->where('code', $request->e_wallet_name);
            })
            ->with(['gateway'])
            ->latest()
            ->first();
                if($lastPayment){
                    if($lastPayment->status==1){
                        $status ="Complete";
                    }elseif($lastPayment->status==3){
                        $status ="Reject";
                    }else{
                        $status ="Pending";
                    }
                    $payment_response = [
                        'status'=>$status,
                        'e_wallet_name'=>$request->e_wallet_name,
                        'amount'=>$lastPayment->amount,
                        'sender'=>$lastPayment->sender,
                        'created_at'=>$lastPayment->created_at,
                        'e_wallet_phone_number'=>$lastPayment->e_wallet_phone_number,
                        'charge'=>$lastPayment->charge,
                        'partner_transection_id'=>$lastPayment->partner_transection_id,
                    ];

                    if(!empty($lastPayment->member_id)){
                        $payment_response['member_id'] = $lastPayment->member_id;
                    }

                    return response()->json($payment_response);
                }
            return response()->json(['message' => 'No payment records found.'], 404);
        }
    }

}
