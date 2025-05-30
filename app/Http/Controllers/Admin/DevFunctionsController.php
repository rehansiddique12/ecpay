<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api;
use App\Models\Payout;
use App\Models\Commission;
use App\Models\EWalletLog;
use Illuminate\Http\Request;
use App\Models\EWalletCharge;
use App\Models\EWalletAccount;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\ParentCommission;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use App\Models\DailyPartnerSummary;
use App\Models\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\DailyPartnerSummaryLog;

class DevFunctionsController extends Controller
{
    public  function payoutAction($id , $status)
    {
        // $this->validate($request, [
        //     'id' => 'required',
        //     'status' => ['required', Rule::in(['2', '3', '4'])],
        // ]);
        DB::beginTransaction();
        try {
            $data = Payout::where('id', $id)->whereIn('transfer_status', [1, 2,3])->with('user', 'gateway')->lockForUpdate()->first();
            // 1 in pending // 2 success
            $basic = (object) config('basic');

            $commit = 0;

            //approved
            if ($status == 2) {
                if (strtolower($data->gateway->name) == "nagad" || strtolower($data->gateway->name) == "rocket" || strtolower($data->gateway->name) == "bkash") {
                    //  $result = $this->checkPayoutAmountWithinTime($data);
                    $this->updateLimits();
                    $this->updateEWallets();

                    $current_time = Carbon::now('Asia/Dhaka');
                    $account = EWalletAccount::where('e_wallet_name', $data->gateway->name)
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
                        $account = EWalletAccount::where('e_wallet_name', $data->gateway->name)
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
                            $account = EWalletAccount::where('e_wallet_name', $data->gateway->name)
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

                    $data->api_id = $data->api_id;
                    $data->e_wallet_name = $data->gateway->name;
                    $data->amount = $data->amount;
                    $data->user_account_no = $user_account_no;
                    $data->e_wallet_phone_number = $account->account_no;
                    $data->e_wallet_type = $account->type;
                    $data->status = 'Pending';
                    // $data->feedback = $request->feedback;
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
                // $data->feedback = $request->feedback;
                $data->save();

                $commit = 1;
                DB::commit();

                //$user = $data->user;

                session()->flash('success', 'Approve Successfully');
            } elseif ($status == 3) {

                if ($data->transfer_status == 3) {
                    DB::rollBack();
                    throw new \Exception("This transaction already rejected!.");
                }

                $data->transfer_status = 3;
                // $data->feedback = $request->feedback;
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
            } elseif ($status == 4) {
                $this->updateLimits();

                if ($data->status == "Complete") {
                    DB::rollBack();
                    throw new \Exception("This transaction already completed!.");
                } else {
                    $data->status = "Complete";
                    $data->completions_at = Carbon::now();
                    $data->transfer_status = 2;
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
                                'remarks' => $data->feedback,
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
}
