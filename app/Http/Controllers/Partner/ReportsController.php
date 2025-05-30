<?php

namespace App\Http\Controllers\Partner;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\Settlement;
use App\Models\ApiTransaction;
use App\Models\PartnerCommission;
use App\Models\Api;
use App\Models\Log;
use App\Models\DailyPartnerSummary;
use Illuminate\Support\Facades\DB;
use App\Models\Payout;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function partner_account_summary(Request $request)
    {
        $from_date = date('Y-m-01');
        // $from_date = '2023-09-01';
        $to_date = date('Y-m-d');
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }

        $user = Auth::guard('partner')->user();
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);


            $from_date_to_search = date('Y-m-d H:i:s', strtotime($currentDateFormatted . ' 00:00:00'));
            $to_date_to_search = date('Y-m-d H:i:s', strtotime($currentDateFormatted . ' 23:59:59'));


            $partnerTimezone = $main_user->timezone;
            $originalTimezone = $partnerTimezone;
            $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
            $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
            $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);

            $deposit = Payment::where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
                ->where('status', 'Complete')
                ->where('api_id', $api_id)
                ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                ->first();

            $withdrawal = Payout::where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
                ->where('status', 'Complete')
                ->where('api_id', $api_id)
                ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                ->first();
            if ($deposit->deposit_amount > 0 || $withdrawal->withdrawal_amount > 0) {
                $data[$count]['partner'] = $api_id;
                $data[$count]['date'] = $currentDateFormatted;
                $data[$count]['deposit_amount'] = $deposit->deposit_amount;
                $data[$count]['deposit_charges'] = $deposit->deposit_charges;
                $data[$count]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
                $data[$count]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
                $data[$count]['total_charges'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges;
                $data[$count]['daily_balance'] = $deposit->deposit_amount - $withdrawal->withdrawal_amount - $deposit->deposit_charges - $withdrawal->withdrawal_charges;
                $count++;
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Partner Account Summary";
        return view('partner.reports.partner_account_summary', compact('pageTitle', 'data', 'from_date', 'to_date'));
    }


    public function partner_account_balance_summary(Request $request)
    {
        // $this->add_daily_partner_summary();
        // exit;

        $from_date = date('Y-m-01');

        // $from_date = '2023-09-01';
        $to_date = date('Y-m-d');

        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }

        $user = Auth::guard('partner')->user();
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            $from_date_to_search = date('Y-m-d H:i:s', strtotime($currentDateFormatted . ' 00:00:00'));
            $to_date_to_search = date('Y-m-d H:i:s', strtotime($currentDateFormatted . ' 23:59:59'));


            $partnerTimezone = $main_user->timezone;
            $originalTimezone = $partnerTimezone;
            $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
            $from_date_to_search = Carbon::parse($from_date_to_search, $originalTimezone)->setTimezone($targetTimezone);
            $to_date_to_search = Carbon::parse($to_date_to_search, $originalTimezone)->setTimezone($targetTimezone);


            $carbonDate = Carbon::createFromFormat('Y-m-d', $currentDateFormatted);
            $oneDayBefore = $carbonDate->subDay();



            $deposit = Payment::where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
                ->where('status', 'Complete')
                ->where('api_id', $api_id)
                ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                ->first();

            $withdrawal = Payout::where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)
                ->where('status', 'Complete')
                ->where('api_id', $api_id)
                ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                ->first();

            $Settlement = Settlement::where('partner_id', $api_id)->where('status', 1)->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)->selectRaw('COALESCE(SUM(amount), 0) as settlement_amount, COALESCE(SUM(charges), 0) as settlement_charges')->first();
            $adjustment = ApiTransaction::where('partner_id', $api_id)->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
            $PartnerCommission = PartnerCommission::where('from_id', $api_id)->where('status', 1)->where('created_at', '>=', $from_date_to_search)->where('created_at', '<=', $to_date_to_search)->selectRaw('COALESCE(SUM(profit), 0) as commission_amount')->first();

            if ($deposit->deposit_amount > 0 || $withdrawal->withdrawal_amount > 0 || $Settlement->settlement_amount > 0 || $adjustment->adjustment_amount > 0 || $PartnerCommission->commission_amount > 0) {
                $data[$count]['partner'] = $api_id;
                $data[$count]['date'] = $currentDateFormatted;
                $data[$count]['opening_balance'] = DailyPartnerSummary::where('api_id', $api_id)->whereDate('created_at', $oneDayBefore)->first()->closing_balance ?? 0.00;
                $data[$count]['deposit_amount'] = $deposit->deposit_amount;
                $data[$count]['deposit_charges'] = $deposit->deposit_charges;
                $data[$count]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
                $data[$count]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
                $data[$count]['settlement_amount'] = $Settlement->settlement_amount;
                $data[$count]['settlement_charges'] = $Settlement->settlement_charges;
                $data[$count]['adjustment'] = $adjustment->adjustment_amount;
                $data[$count]['adjustment_charges'] = $adjustment->adjustment_charges;
                $data[$count]['commission'] = $PartnerCommission->commission_amount;
                $data[$count]['total_charges'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges + $Settlement->settlement_charges + $adjustment->adjustment_charges;
                $data[$count]['closing_balance'] = $data[$count]['opening_balance'] + $data[$count]['adjustment'] - $data[$count]['adjustment_charges'] + $data[$count]['commission'] + $data[$count]['deposit_amount'] - $data[$count]['deposit_charges'] - $data[$count]['withdrawal_amount'] - $data[$count]['withdrawal_charges'] - $data[$count]['settlement_amount'] - $data[$count]['settlement_charges'];
                $count++;
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Partner Account Balance Summary";
        return view('partner.reports.partner_account_balance_summary', compact('pageTitle', 'data', 'from_date', 'to_date'));
    }

    public function logs(Request $request)
    {
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d 00:00');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d H:i');
        $orderval = $request->get('order', 'desc');

        $user = Auth::guard('partner')->user();
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        if (!$main_user) {
            abort(403, 'Unauthorized.');
        }

        $api_id = $main_user->id;

        // Step 1: Fetch all logs in date range
        $logs = Log::where('partner_id', $api_id)
            ->whereBetween(DB::raw('created_at'), [$from_date, $to_date])
            ->orderBy('created_at', $orderval)
            ->get();

        if ($logs->isEmpty()) {
            return view('partner.reports.logs', [
                'pageTitle' => 'Partner Balance Logs',
                'filter_data' => [],
                'from_date' => $from_date,
                'to_date' => $to_date,
                'orderval' => $orderval
            ]);
        }

        // Step 2: Group logs by transaction type
        $logsByType = $logs->groupBy('transection_type');

        // Step 3: Preload data for each type
        $transectionData = [];

        if (isset($logsByType[1])) {
            $ids = $logsByType[1]->pluck('transection_id')->unique();
            $transectionData[1] = Payment::whereIn('id', $ids)->get()->keyBy('id');
        }

        if (isset($logsByType[2]) || isset($logsByType[7])) {
            $ids = collect($logsByType[2] ?? [])->merge($logsByType[7] ?? [])->pluck('transection_id')->unique();
            $transectionData[2] = Payout::whereIn('id', $ids)->get()->keyBy('id');
        }

        if (isset($logsByType[3])) {
            $ids = $logsByType[3]->pluck('transection_id')->unique();
            $transectionData[3] = ApiTransaction::whereIn('id', $ids)->get()->keyBy('id');
        }

        if (isset($logsByType[4])) {
            $ids = $logsByType[4]->pluck('transection_id')->unique();
            $transectionData[4] = Settlement::whereIn('id', $ids)->get()->keyBy('id');
        }

        if (isset($logsByType[5])) {
            $ids = $logsByType[5]->pluck('transection_id')->unique();
            $commissions = PartnerCommission::whereIn('id', $ids)->get();
            $transectionData[5] = $commissions->keyBy('id');
            // Preload sender data
            $apiIds = $commissions->pluck('api_id')->unique();
            $senders = Api::whereIn('id', $apiIds)->get()->keyBy('id');
            $transectionData['commission_senders'] = $senders;
        }

        // Step 4: Prepare final filtered data
        $filter_data = [];

        foreach ($logs as $item) {
            $entry = [
                'id' => $item->id,
                'date_time' => $item->date_time,
                'final_amount' => $item->final_amount,
                'balance' => $item->balance,
                'transection_type' => $item->transection_type,
                'transection_id' => $item->transection_id,
                'partner_id' => $item->partner_id,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'source' => $item->source,
                'amount' => '',
                'charge' => '',
                'sender' => '',
                'e_wallet_name' => '',
                'e_wallet_phone_number' => '',
                'e_wallet_type' => '',
                'partner_transection_id' => '',
                'txn_id' => '',
                'txn_created_at' => '',
                'txn_updated_at' => '',
            ];

            $type = $item->transection_type;
            $tid = $item->transection_id;

            switch ($type) {
                case 1:
                    $trx = $transectionData[1][$tid] ?? null;
                    if ($trx) {
                        $entry['amount'] = $trx->amount;
                        $entry['charge'] = $trx->charge;
                        $entry['sender'] = $trx->sender;
                        $entry['e_wallet_name'] = $trx->e_wallet_name;
                        $entry['e_wallet_phone_number'] = $trx->e_wallet_phone_number;
                        $entry['e_wallet_type'] = $trx->e_wallet_type;
                        $entry['partner_transection_id'] = $trx->partner_transection_id;
                        $entry['txn_id'] = $trx->txn_id;
                        $entry['txn_created_at'] = $trx->created_at;
                        $entry['txn_updated_at'] = $trx->trans_complete_date;
                    }
                    break;
                case 2:
                case 7:
                    $trx = $transectionData[2][$tid] ?? null;
                    if ($trx) {
                        $entry['amount'] = $trx->amount;
                        $entry['charge'] = $trx->charge;
                        $entry['sender'] = $trx->user_account_no;
                        $entry['e_wallet_name'] = $trx->e_wallet_name;
                        $entry['e_wallet_phone_number'] = $trx->e_wallet_phone_number;
                        $entry['e_wallet_type'] = $trx->e_wallet_type;
                        $entry['partner_transection_id'] = $trx->partner_transection_id;
                        $entry['txn_id'] = $trx->txn_id;
                        $entry['txn_created_at'] = $trx->created_at;
                        $entry['txn_updated_at'] = $trx->updated_at;
                    }
                    break;
                case 3:
                    $trx = $transectionData[3][$tid] ?? null;
                    if ($trx) {
                        $entry['amount'] = $trx->amount;
                        $entry['charge'] = $trx->charges;
                        $entry['e_wallet_name'] = $trx->source;
                        $entry['txn_id'] = $trx->txn;
                        $entry['txn_created_at'] = $trx->created_at;
                        $entry['txn_updated_at'] = $trx->updated_at;
                    }
                    break;
                case 4:
                    $trx = $transectionData[4][$tid] ?? null;
                    if ($trx) {
                        $entry['amount'] = $trx->amount;
                        $entry['charge'] = $trx->charges;
                        $entry['sender'] = $trx->account_no;
                        $entry['e_wallet_name'] = $trx->source_name;
                        $entry['e_wallet_type'] = $trx->source;
                        $entry['txn_created_at'] = $trx->created_at;
                        $entry['txn_updated_at'] = $trx->updated_at;
                    }
                    break;
                case 5:
                    $trx = $transectionData[5][$tid] ?? null;
                    if ($trx) {
                        $sender = $transectionData['commission_senders'][$trx->api_id] ?? null;
                        $entry['sender'] = $sender?->name ?? '';
                        $entry['amount'] = $trx->amount;
                        $entry['charge'] = $trx->charges;
                        $entry['e_wallet_type'] = $trx->type == 1 ? 'Deposit' : ($trx->type == 2 ? 'Withdrawal' : '');
                        $entry['txn_created_at'] = $trx->created_at;
                        $entry['txn_updated_at'] = $trx->updated_at;
                    }
                    break;
            }

            $filter_data[] = $entry;
        }

        return view('partner.reports.logs', [
            'pageTitle' => 'Partner Balance Logs',
            'filter_data' => $filter_data,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'orderval' => $orderval,
        ]);
    }




    public function export_excel_record(Request $request){

        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d 00:00');
       $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d H:i');
       $sort_by = $request->get('sort_by', 'created_at');
       $order = $request->get('order', 'desc');

       $user = Auth::guard('partner')->user();
       $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
       $api_id = $main_user->id;


       // Header for CSV file
       $headers = [
           'Content-Type' => 'text/csv',
           'Content-Disposition' => 'attachment; filename="transaction_completed_at_log_report.csv"',
       ];

       $data = Log::where('partner_id', $user->id)->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])->orderBy('logs.created_at', 'desc')->get();
       // dd($data );
       $filter_data = [];
       foreach ($data as $key => $item) {
           $filter_data[$key]['id'] =  $item->id;
           // $filter_data[$key]['partner'] =  $item->api->name;
           $filter_data[$key]['date_time'] =  $item->date_time;
           $filter_data[$key]['final_amount'] =  $item->final_amount;
           $filter_data[$key]['balance'] =  $item->balance;
           $filter_data[$key]['transection_type'] =  $item->transection_type;
           $filter_data[$key]['transection_id'] =  $item->transection_id;
           $filter_data[$key]['partner_id'] =  $item->partner_id;
           $filter_data[$key]['created_at'] =  $item->created_at;
           $filter_data[$key]['updated_at'] =  $item->updated_at;
           $filter_data[$key]['source'] =  $item->source;

           $filter_data[$key]['amount'] =  "";
           $filter_data[$key]['charge'] =  "";
           $filter_data[$key]['sender'] =  "";
           $filter_data[$key]['e_wallet_name'] =  "";
           $filter_data[$key]['e_wallet_phone_number'] =  "";
           $filter_data[$key]['e_wallet_type'] =  "";
           $filter_data[$key]['partner_transection_id'] =  "";
           $filter_data[$key]['txn_id'] =  "";
           $filter_data[$key]['txn_created_at'] =  "";

               if($item->transection_type==1){
                   $deposits = Payment::where('id', $item->transection_id)->first();
                   if($deposits){
                       $filter_data[$key]['amount'] =  $deposits->amount;
                       $filter_data[$key]['charge'] =  $deposits->charge;
                       $filter_data[$key]['sender'] =  $deposits->sender;
                       $filter_data[$key]['e_wallet_name'] =  $deposits->e_wallet_name;
                       $filter_data[$key]['e_wallet_phone_number'] =  $deposits->e_wallet_phone_number;
                       $filter_data[$key]['e_wallet_type'] =  $deposits->e_wallet_type;
                       $filter_data[$key]['partner_transection_id'] =  $deposits->partner_transection_id;
                       $filter_data[$key]['txn_id'] =  $deposits->txn_id;
                       $filter_data[$key]['txn_created_at'] =  $deposits->created_at;
                       $filter_data[$key]['txn_updated_at'] =  $deposits->trans_complete_date;
                       $filter_data[$key]['transection_id'] =  $deposits->id;
                   }
               }elseif($item->transection_type==2){
                   $withdrawal = Payout::where('id', $item->transection_id)->first();
                   if($withdrawal){
                       $filter_data[$key]['amount'] =  $withdrawal->amount;
                       $filter_data[$key]['charge'] =  $withdrawal->charge;
                       $filter_data[$key]['sender'] =  $withdrawal->user_account_no;
                       $filter_data[$key]['e_wallet_name'] =  $withdrawal->e_wallet_name;
                       $filter_data[$key]['e_wallet_phone_number'] =  $withdrawal->e_wallet_phone_number;
                       $filter_data[$key]['e_wallet_type'] =  $withdrawal->e_wallet_type;
                       $filter_data[$key]['partner_transection_id'] =  $withdrawal->partner_transection_id;
                       $filter_data[$key]['txn_id'] =  $withdrawal->txn_id;
                       $filter_data[$key]['txn_created_at'] =  $withdrawal->created_at;
                       $filter_data[$key]['txn_updated_at'] =  $withdrawal->completions_at;
                       $filter_data[$key]['transection_id'] =  $withdrawal->id;
                   }
               }elseif($item->transection_type==3){
                   $ApiTransaction = ApiTransaction::where('id', $item->transection_id)->first();
                   if($ApiTransaction){
                       $filter_data[$key]['amount'] =  $ApiTransaction->amount;
                       $filter_data[$key]['charge'] =  $ApiTransaction->charges;
                       $filter_data[$key]['e_wallet_name'] =  $ApiTransaction->source;
                       $filter_data[$key]['txn_id'] =  $ApiTransaction->txn;
                       $filter_data[$key]['txn_created_at'] =  $ApiTransaction->created_at;
                       $filter_data[$key]['txn_updated_at'] =  $ApiTransaction->updated_at;
                       $filter_data[$key]['transection_id'] =  $ApiTransaction->id;
                   }
               }elseif($item->transection_type==4){
                   $Settlement = Settlement::where('id', $item->transection_id)->first();
                   if($Settlement){
                       $filter_data[$key]['amount'] =  $Settlement->amount;
                       $filter_data[$key]['charge'] =  $Settlement->charges;
                       $filter_data[$key]['sender'] =  $Settlement->account_no;
                       $filter_data[$key]['e_wallet_name'] =  $Settlement->source_name;
                       $filter_data[$key]['e_wallet_type'] =  $Settlement->source;
                       $filter_data[$key]['txn_created_at'] =  $Settlement->created_at;
                       $filter_data[$key]['txn_updated_at'] =  $Settlement->updated_at;
                       $filter_data[$key]['transection_id'] =  $Settlement->id;
                   }
               }elseif($item->transection_type==5){
                   $PartnerCommission = PartnerCommission::where('id', $item->transection_id)->first();
                   if($PartnerCommission){
                       $sender = Api::where('id', $PartnerCommission->api_id)->first();
                       if($sender){
                           $filter_data[$key]['sender'] =  $sender->name;
                       }
                       $filter_data[$key]['amount'] =  $PartnerCommission->amount;
                       $filter_data[$key]['charge'] =  $PartnerCommission->charges;
                       if($PartnerCommission->type==1){
                           $filter_data[$key]['e_wallet_type'] =  "Deposit";
                       }
                       if($PartnerCommission->type==2){
                           $filter_data[$key]['e_wallet_type'] =  "Withdrawal";
                       }
                       $filter_data[$key]['txn_created_at'] =  $PartnerCommission->created_at;
                       $filter_data[$key]['txn_updated_at'] =  $PartnerCommission->updated_at;
                       $filter_data[$key]['transection_id'] =  $PartnerCommission->id;
                   }
               }elseif($item->transection_type==7){
                   $withdrawal = Payout::where('id', $item->transection_id)->first();
                   if($withdrawal){
                       $filter_data[$key]['amount'] =  $withdrawal->amount;
                       $filter_data[$key]['charge'] =  $withdrawal->charge;
                       $filter_data[$key]['sender'] =  $withdrawal->user_account_no;
                       $filter_data[$key]['e_wallet_name'] =  $withdrawal->e_wallet_name;
                       $filter_data[$key]['e_wallet_phone_number'] =  $withdrawal->e_wallet_phone_number;
                       $filter_data[$key]['e_wallet_type'] =  $withdrawal->e_wallet_type;
                       $filter_data[$key]['partner_transection_id'] =  $withdrawal->partner_transection_id;
                       $filter_data[$key]['txn_id'] =  $withdrawal->txn_id;
                       $filter_data[$key]['txn_created_at'] =  $withdrawal->created_at;
                       $filter_data[$key]['txn_updated_at'] =  $withdrawal->updated_at;
                       $filter_data[$key]['transection_id'] =  $withdrawal->id;
                   }
               }

       }

       return response()->stream(function () use ($filter_data) {
           // Clear any previous output
           if (ob_get_level() > 0) {
               ob_end_clean();
           }

           // Open the output stream
           $handle = fopen('php://output', 'w');

           // Add the CSV header
           fputcsv($handle, ['Transaction Date', 'Completed Date', 'Txn No', 'Partner Txn No', 'Account No', 'Source', 'Type', 'E-Wallet Acc No', 'Amount', 'Chargers', 'Final Amount', 'Balance', 'Transaction Type']);


           // Loop through data and write to CSV
           foreach ($filter_data as $row) {

               if ($row['transection_type'] == 1) {
                   $type = "Deposit";
               } elseif ($row['transection_type'] == 2) {
                   $type = "Withdrawal";
               } elseif ($row['transection_type'] == 3) {
                   $type = "Adjustment";
               } elseif ($row['transection_type'] == 4) {
                   $type = "Settlement";
               } elseif ($row['transection_type'] == 5) {
                   $type = "Commission";
               } elseif ($row['transection_type'] == 7) {
                   $type = "Withdrawal Refunded";
               } else {
                   $type = $row['transection_type'];
               }

               fputcsv($handle, [
                   $row['txn_created_at'],
                   $row['txn_updated_at'],
                   $row['transection_id'],
                   $row['partner_transection_id'],
                   $row['sender'],
                   $row['e_wallet_name'],
                   $row['e_wallet_type'],
                   $row['e_wallet_phone_number'],
                   $row['amount'],
                   $row['charge'],
                   number_format($row['final_amount'], 2),
                   number_format($row['balance'], 2),
                   $type,
               ]);
           }

           // Close the handle
           fclose($handle);
       }, 200, [
           "Content-Type" => "text/csv",
           "Content-Disposition" => "attachment; filename=\"transaction_created_at_log_report.csv\"",
           "Cache-Control" => "no-cache, no-store, must-revalidate",
           "Pragma" => "no-cache",
           "Expires" => "0"
       ]);



       return response()->stream($callback, 200, $headers);
   }


       public function log_completions(Request $request)
    {
        // date_default_timezone_set('Asia/Dhaka');
        // Set the date format to include time
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d 00:00');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d H:i');
        $sort_by = $request->get('sort_by', 'updated_at');
        $order = $request->get('order', 'desc');
        // $order = "desc";


        $orignal_from_date = $from_date;
        $orignal_to_date = $to_date;

        $website = "";

        $user = Auth::guard('partner')->user();
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $partnerTimezone = $main_user->timezone;
        $api_id = $main_user->id;


        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date = Carbon::parse($from_date, $originalTimezone)->setTimezone($targetTimezone)->format('Y-m-d H:i');
        $to_date = Carbon::parse($to_date, $originalTimezone)->setTimezone($targetTimezone)->format('Y-m-d H:i');

        $previous_date = Carbon::parse($from_date)->subDay()->format('Y-m-d');
        $opening_balance = DailyPartnerSummary::select('closing_balance')->where('api_id', $api_id)->whereDate('created_at', $previous_date)->first();


        $final_data = [];

        if ($order === 'asc') {
            $currentDate = strtotime($from_date);
            $endDate = strtotime($to_date);
        } else {
            $currentDate = strtotime($to_date);
            $endDate = strtotime($from_date);
        }

        // echo $order.'<br>';
        // echo $from_date.'<br>';
        // echo $to_date.'<br>';

        $count = 0;
        $t_count = 0;
        $total_amount = 0;
        while ($order === 'asc' ? $currentDate <= $endDate : $currentDate >= $endDate) {
            $data = [];

            $sDate = Carbon::createFromTimestamp($currentDate);
            $sDate->setTimezone('Asia/Dhaka');
            $carbonDate = $sDate->copy()->format('Y-m-d H:i:s');

            $t_count ++;
            if($order == 'asc'){

                $go = Carbon::createFromFormat('Y-m-d H:i', $to_date);
                $lastDate = $go->copy()->format('Y-m-d');
                $lastTime = $go->copy()->format('H:i').':59';

                if($t_count >= 2)
                {
                    $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)
                    ->startOfDay()->format('Y-m-d H:i:s');
                    $sign1 = ">=";
                    $sign2 = "<=";
                    // dd($carbonDate);
                }

                // dd($carbonDate);
                if($sDate->copy()->format('Y-m-d') == $lastDate)
                {
                    // $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)->endOfDay();

                    $datePart = Carbon::createFromFormat('Y-m-d', substr($to_date, 0, 10));
                    $timePart = Carbon::createFromFormat('H:i', substr($to_date, 11));
                    $a = $datePart->format('Y-m-d') . ' ' . $timePart->format('H:i').":59";
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $a);

                    $sign1 = ">=";
                    $sign2 = "<=";
                }
                else
                {
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)->endOfDay();
                    $sign1 = ">=";
                    $sign2 = "<=";
                }



            }
            else
            {
                $currentDateOnly = Carbon::createFromTimestamp($currentDate)->format('Y-m-d');
                $endDateOnly = Carbon::createFromTimestamp($endDate)->format('Y-m-d');

                $carbonDate = $sDate->copy()->endOfDay()->format('Y-m-d H:i:s');

                // dd($endDateOnly);
                if ($currentDateOnly === $endDateOnly) {




                    $datePart = Carbon::createFromFormat('Y-m-d', substr($from_date, 0, 10));
                    $timePart = Carbon::createFromFormat('H:i', substr($from_date, 11));
                    $a = $datePart->format('Y-m-d') . ' ' . $timePart->format('H:i:s');
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $a);

                    $sign1 = "<=";
                    $sign2 = ">=";
                }
                else
                {
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)
                    ->startOfDay();
                    $sign1 = "<=";
                    $sign2 = ">=";
                }
            }

            // echo '<br>';
            // echo '<pre>';
            // print_r($carbonDate);
            // echo '</pre>';

            // echo '<br>';
            // echo '<pre>';
            // print_r($to_date1->format('Y-m-d H:i:s'));
            // echo '</pre>';

            $deposits = Payment::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('trans_complete_date', $sign1 , $carbonDate)
                ->where('trans_complete_date', $sign2 , $to_date1)
                ->orderBy('updated_at', $order)
                ->get();

            //completion_at
            $totalDeposits = Payment::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('trans_complete_date', $sign1, $carbonDate)
                ->where('trans_complete_date', $sign2, $to_date1)
                ->sum(DB::raw('amount - charge'));

            //dd($totalDeposits);

            foreach ($deposits as $deposit) {
                $data[$count]['txn_created_at'] = $deposit->created_at;
                $data[$count]['updated_at'] =  $deposit->trans_complete_date;
                $data[$count]['transection_id'] = $deposit->id;
                $data[$count]['partner_transection_id'] =  $deposit->partner_transection_id;
                $data[$count]['sender'] =  $deposit->sender;
                $data[$count]['e_wallet_name'] =  $deposit->e_wallet_name;
                $data[$count]['e_wallet_type'] =  $deposit->e_wallet_type;
                $data[$count]['e_wallet_phone_number'] =  $deposit->e_wallet_phone_number;
                $data[$count]['amount'] =  $deposit->amount;
                $data[$count]['charge'] =  $deposit->charge;
                $data[$count]['final_amount'] = $deposit->amount - $deposit->charge;
                $data[$count]['transection_type'] = 1;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $withdrawals = Payout::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('completions_at', $sign1, $carbonDate)
                ->where('completions_at', $sign2, $to_date1)
                ->orderBy('updated_at', $order)
                ->get();


            //created_at completions_at
            $totalWithdrawals = Payout::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('completions_at', $sign1, $carbonDate)
                ->where('completions_at', $sign2, $to_date1)
                ->sum(DB::raw('amount + charge'));
            //dd($totalWithdrawals);


            foreach ($withdrawals as $withdrawal) {
                $data[$count]['txn_created_at'] =  $withdrawal->created_at;
                $data[$count]['updated_at'] = $withdrawal->completions_at;
                $data[$count]['transection_id'] = $withdrawal->id;
                $data[$count]['partner_transection_id'] =  $withdrawal->partner_transection_id;
                $data[$count]['sender'] =  $withdrawal->user_account_no;
                $data[$count]['e_wallet_name'] =  $withdrawal->e_wallet_name;
                $data[$count]['e_wallet_type'] =  $withdrawal->e_wallet_type;
                $data[$count]['e_wallet_phone_number'] =  $withdrawal->e_wallet_phone_number;
                $data[$count]['amount'] =  $withdrawal->amount;
                $data[$count]['charge'] =  $withdrawal->charge;
                $data[$count]['date_time'] = $withdrawal->created_at->timestamp;
                $data[$count]['final_amount'] = -($withdrawal->amount + $withdrawal->charge);
                $data[$count]['transection_type'] = 2;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $ApiTransactions = ApiTransaction::where('partner_id', $api_id)
                ->where('created_at', $sign1, $carbonDate)
                ->where('created_at', $sign2, $to_date1)
                ->get();

            $totalAmount = ApiTransaction::where('partner_id', $api_id)
                ->where('created_at', $sign1, $carbonDate)
                ->where('created_at', $sign2, $to_date1)
                ->sum('amount');
            //dd($totalAmount);

            foreach ($ApiTransactions as $ApiTransaction) {
                $data[$count]['txn_created_at'] =  $ApiTransaction->created_at;
                $data[$count]['updated_at'] =  $ApiTransaction->updated_at;
                $data[$count]['transection_id'] = $ApiTransaction->id;
                $data[$count]['partner_transection_id'] =  "";
                $data[$count]['sender'] =  "";
                $data[$count]['e_wallet_name'] =  $ApiTransaction->source;
                $data[$count]['e_wallet_type'] =  "";
                $data[$count]['e_wallet_phone_number'] =  "";
                $data[$count]['amount'] = $ApiTransaction->amount;
                $data[$count]['charge'] = $ApiTransaction->charges;
                $data[$count]['final_amount'] = $ApiTransaction->amount - $ApiTransaction->charges;
                $data[$count]['transection_type'] = 3;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $Settlements = Settlement::where('status', 1)
                ->where('partner_id', $api_id)
                ->where('updated_at', $sign1, $carbonDate)
                ->where('updated_at', $sign2, $to_date1)
                ->orderBy('updated_at', $order)
                ->get();

            foreach ($Settlements as $Settlement) {
                $data[$count]['txn_created_at'] =  $Settlement->created_at;
                $data[$count]['updated_at'] =  $Settlement->updated_at;
                $data[$count]['transection_id'] = $Settlement->id;
                $data[$count]['partner_transection_id'] =  "";
                $data[$count]['sender'] =  $Settlement->account_no;
                $data[$count]['e_wallet_name'] =  $Settlement->source_name;
                $data[$count]['e_wallet_type'] =  $Settlement->source;
                $data[$count]['e_wallet_phone_number'] =  "";
                $data[$count]['amount'] =  $Settlement->amount;
                $data[$count]['charge'] =  $Settlement->charges;
                $data[$count]['final_amount'] = - ($Settlement->amount + $Settlement->charge);
                $data[$count]['transection_type'] = 4;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $PartnerCommissions = PartnerCommission::where('status', 1)
                ->where('from_id', $api_id)
                ->where('updated_at', $sign1, $carbonDate)
                ->where('updated_at', $sign2, $to_date1)
                ->orderBy('updated_at', $order)
                ->get();

            foreach ($PartnerCommissions as $PartnerCommission) {
                $data[$count]['txn_created_at'] =  $PartnerCommission->created_at;
                $data[$count]['updated_at'] =  $PartnerCommission->updated_at;
                $data[$count]['transection_id'] = $PartnerCommission->id;
                $data[$count]['partner_transection_id'] =  "";
                $sender = Api::where('id', $PartnerCommission->api_id)->first();
                $data[$count]['sender'] =  "";
                if ($sender) {
                    $data[$count]['sender'] =  $sender->name;
                }
                $data[$count]['e_wallet_name'] =  "";
                $data[$count]['e_wallet_type'] =  "";
                if ($PartnerCommission->type == 1) {
                    $data[$count]['e_wallet_type'] =  "Deposit";
                }
                if ($PartnerCommission->type == 2) {
                    $data[$count]['e_wallet_type'] =  "Withdrawal";
                }
                $data[$count]['e_wallet_phone_number'] =  "";
                $data[$count]['amount'] =  $PartnerCommission->amount;
                $data[$count]['charge'] =  $PartnerCommission->charges;
                $data[$count]['final_amount'] = $PartnerCommission->profit;
                $data[$count]['transection_type'] = 5;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            if ($order === 'asc') {
                $currentDate = strtotime('+1 day', $currentDate);
            } else {
                $currentDate = strtotime('-1 day', $currentDate);
            }


            usort($data, function ($a, $b) use ($sort_by, $order) {
                if (!isset($a[$sort_by]) || !isset($b[$sort_by])) {
                    return 0;
                }

                if ($order === 'asc') {
                    return strtotime($a[$sort_by]) - strtotime($b[$sort_by]);
                } else {
                    return strtotime($b[$sort_by]) - strtotime($a[$sort_by]);
                }
            });

            $final_data = array_merge($final_data, $data);
        }
        // exit;

        $closing_balance = isset($opening_balance->closing_balance) ? $opening_balance->closing_balance : 0;
        $pageTitle = "Transactions Completions Logs";

        $from_date = $orignal_from_date;
        $to_date = $orignal_to_date;

        return view('partner.reports.log_completions', compact('pageTitle', 'final_data', 'from_date', 'to_date', 'order', 'closing_balance', 'total_amount'));
    }

         public function export_excel_record_completions(Request $request){

        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d H:i');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d H:i');
        $sort_by = $request->get('sort_by', 'updated_at');
        $order = $request->get('order', 'desc');


        $user = Auth::guard('partner')->user();
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;


        // Header for CSV file
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transaction_completed_at_log_report.csv"',
        ];

        $website = "";

        $user = Auth::guard('partner')->user();
        $main_user = Api::where('api_key', $user->api_key)->where('type', 'Admin')->first();
        $api_id = $main_user->id;
        // $data = Log::orderBy('created_at', 'asc')->where('partner_id', $api_id)->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])->with('api')->get();

        $previous_date = Carbon::parse($from_date)->subDay()->format('Y-m-d');
        $opening_balance = DailyPartnerSummary::select('closing_balance')->where('api_id' , $api_id)->whereDate('created_at' , $previous_date)->first();

        if ($order === 'asc') {
            $currentDate = strtotime($from_date);
            $endDate = strtotime($to_date);
        } else {
            $currentDate = strtotime($to_date);
            $endDate = strtotime($from_date);
        }

        $final_data = [];
        $count = 0;
        $t_count = 0;
        $total_amount = 0;
        while ($order === 'asc' ? $currentDate <= $endDate : $currentDate >= $endDate) {
            $data = [];

            $sDate = Carbon::createFromTimestamp($currentDate);
            $sDate->setTimezone('Asia/Dhaka');
            $carbonDate = $sDate->copy()->format('Y-m-d H:i:s');

            $t_count ++;
            if($order == 'asc'){

                $go = Carbon::createFromFormat('Y-m-d H:i', $to_date);
                $lastDate = $go->copy()->format('Y-m-d');
                $lastTime = $go->copy()->format('H:i').':59';

                if($t_count >= 2)
                {
                    $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)
                    ->startOfDay()->format('Y-m-d H:i:s');
                    $sign1 = ">=";
                    $sign2 = "<=";
                    // dd($carbonDate);
                }

                // dd($carbonDate);
                if($sDate->copy()->format('Y-m-d') == $lastDate)
                {
                    // $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)->endOfDay();

                    $datePart = Carbon::createFromFormat('Y-m-d', substr($to_date, 0, 10));
                    $timePart = Carbon::createFromFormat('H:i', substr($to_date, 11));
                    $a = $datePart->format('Y-m-d') . ' ' . $timePart->format('H:i').":59";
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $a);

                    $sign1 = ">=";
                    $sign2 = "<=";
                }
                else
                {
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)->endOfDay();
                    $sign1 = ">=";
                    $sign2 = "<=";
                }



            }
            else
            {
                $currentDateOnly = Carbon::createFromTimestamp($currentDate)->format('Y-m-d');
                $endDateOnly = Carbon::createFromTimestamp($endDate)->format('Y-m-d');

                $carbonDate = $sDate->copy()->endOfDay()->format('Y-m-d H:i:s');

                // dd($endDateOnly);
                if ($currentDateOnly === $endDateOnly) {




                    $datePart = Carbon::createFromFormat('Y-m-d', substr($from_date, 0, 10));
                    $timePart = Carbon::createFromFormat('H:i', substr($from_date, 11));
                    $a = $datePart->format('Y-m-d') . ' ' . $timePart->format('H:i:s');
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $a);

                    $sign1 = "<=";
                    $sign2 = ">=";
                }
                else
                {
                    $to_date1 = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate)
                    ->startOfDay();
                    $sign1 = "<=";
                    $sign2 = ">=";
                }
            }

            $deposits = Payment::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('trans_complete_date', $sign1 , $carbonDate)
                ->where('trans_complete_date', $sign2 , $to_date1)
                ->orderBy('updated_at', $order)
                ->get();

            //completion_at
            $totalDeposits = Payment::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('trans_complete_date', $sign1, $carbonDate)
                ->where('trans_complete_date', $sign2, $to_date1)
                ->sum(DB::raw('amount - charge'));

            //dd($totalDeposits);

            foreach ($deposits as $deposit) {
                $data[$count]['txn_created_at'] = $deposit->completion_at;
                $data[$count]['updated_at'] =  $deposit->trans_complete_date;
                $data[$count]['transection_id'] = $deposit->id;
                $data[$count]['partner_transection_id'] =  $deposit->partner_transection_id;
                $data[$count]['sender'] =  $deposit->sender;
                $data[$count]['e_wallet_name'] =  $deposit->e_wallet_name;
                $data[$count]['e_wallet_type'] =  $deposit->e_wallet_type;
                $data[$count]['e_wallet_phone_number'] =  $deposit->e_wallet_phone_number;
                $data[$count]['amount'] =  $deposit->amount;
                $data[$count]['charge'] =  $deposit->charge;
                $data[$count]['final_amount'] = $deposit->amount - $deposit->charge;
                $data[$count]['transection_type'] = 1;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $withdrawals = Payout::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('completions_at', $sign1, $carbonDate)
                ->where('completions_at', $sign2, $to_date1)
                ->orderBy('updated_at', $order)
                ->get();


            //created_at completions_at
            $totalWithdrawals = Payout::where('status', 'Complete')
                ->where('api_id', $api_id)
                ->where('completions_at', $sign1, $carbonDate)
                ->where('completions_at', $sign2, $to_date1)
                ->sum(DB::raw('amount + charge'));
            //dd($totalWithdrawals);


            foreach ($withdrawals as $withdrawal) {
                $data[$count]['txn_created_at'] =  $withdrawal->created_at;
                $data[$count]['updated_at'] = $withdrawal->completions_at;
                $data[$count]['transection_id'] = $withdrawal->id;
                $data[$count]['partner_transection_id'] =  $withdrawal->partner_transection_id;
                $data[$count]['sender'] =  $withdrawal->user_account_no;
                $data[$count]['e_wallet_name'] =  $withdrawal->e_wallet_name;
                $data[$count]['e_wallet_type'] =  $withdrawal->e_wallet_type;
                $data[$count]['e_wallet_phone_number'] =  $withdrawal->e_wallet_phone_number;
                $data[$count]['amount'] =  $withdrawal->amount;
                $data[$count]['charge'] =  $withdrawal->charge;
                $data[$count]['date_time'] = $withdrawal->created_at->timestamp;
                $data[$count]['final_amount'] = -($withdrawal->amount + $withdrawal->charge);
                $data[$count]['transection_type'] = 2;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $ApiTransactions = ApiTransaction::where('partner_id', $api_id)
                ->where('created_at', $sign1, $carbonDate)
                ->where('created_at', $sign2, $to_date1)
                ->get();

            $totalAmount = ApiTransaction::where('partner_id', $api_id)
                ->where('created_at', $sign1, $carbonDate)
                ->where('created_at', $sign2, $to_date1)
                ->sum('amount');
            //dd($totalAmount);

            foreach ($ApiTransactions as $ApiTransaction) {
                $data[$count]['txn_created_at'] =  $ApiTransaction->created_at;
                $data[$count]['updated_at'] =  $ApiTransaction->updated_at;
                $data[$count]['transection_id'] = $ApiTransaction->id;
                $data[$count]['partner_transection_id'] =  "";
                $data[$count]['sender'] =  "";
                $data[$count]['e_wallet_name'] =  $ApiTransaction->source;
                $data[$count]['e_wallet_type'] =  "";
                $data[$count]['e_wallet_phone_number'] =  "";
                $data[$count]['amount'] = $ApiTransaction->amount;
                $data[$count]['charge'] = $ApiTransaction->charges;
                $data[$count]['final_amount'] = $ApiTransaction->amount - $ApiTransaction->charges;
                $data[$count]['transection_type'] = 3;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $Settlements = Settlement::where('status', 1)
                ->where('partner_id', $api_id)
                ->where('updated_at', $sign1, $carbonDate)
                ->where('updated_at', $sign2, $to_date1)
                ->orderBy('updated_at', $order)
                ->get();

            foreach ($Settlements as $Settlement) {
                $data[$count]['txn_created_at'] =  $Settlement->created_at;
                $data[$count]['updated_at'] =  $Settlement->updated_at;
                $data[$count]['transection_id'] = $Settlement->id;
                $data[$count]['partner_transection_id'] =  "";
                $data[$count]['sender'] =  $Settlement->account_no;
                $data[$count]['e_wallet_name'] =  $Settlement->source_name;
                $data[$count]['e_wallet_type'] =  $Settlement->source;
                $data[$count]['e_wallet_phone_number'] =  "";
                $data[$count]['amount'] =  $Settlement->amount;
                $data[$count]['charge'] =  $Settlement->charges;
                $data[$count]['final_amount'] = - ($Settlement->amount + $Settlement->charge);
                $data[$count]['transection_type'] = 4;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            $PartnerCommissions = PartnerCommission::where('status', 1)
                ->where('from_id', $api_id)
                ->where('updated_at', $sign1, $carbonDate)
                ->where('updated_at', $sign2, $to_date1)
                ->orderBy('updated_at', $order)
                ->get();

            foreach ($PartnerCommissions as $PartnerCommission) {
                $data[$count]['txn_created_at'] =  $PartnerCommission->created_at;
                $data[$count]['updated_at'] =  $PartnerCommission->updated_at;
                $data[$count]['transection_id'] = $PartnerCommission->id;
                $data[$count]['partner_transection_id'] =  "";
                $sender = Api::where('id', $PartnerCommission->api_id)->first();
                $data[$count]['sender'] =  "";
                if ($sender) {
                    $data[$count]['sender'] =  $sender->name;
                }
                $data[$count]['e_wallet_name'] =  "";
                $data[$count]['e_wallet_type'] =  "";
                if ($PartnerCommission->type == 1) {
                    $data[$count]['e_wallet_type'] =  "Deposit";
                }
                if ($PartnerCommission->type == 2) {
                    $data[$count]['e_wallet_type'] =  "Withdrawal";
                }
                $data[$count]['e_wallet_phone_number'] =  "";
                $data[$count]['amount'] =  $PartnerCommission->amount;
                $data[$count]['charge'] =  $PartnerCommission->charges;
                $data[$count]['final_amount'] = $PartnerCommission->profit;
                $data[$count]['transection_type'] = 5;
                $total_amount += $data[$count]['final_amount'];
                $count++;
            }

            if ($order === 'asc') {
                $currentDate = strtotime('+1 day', $currentDate);
            } else {
                $currentDate = strtotime('-1 day', $currentDate);
            }


            usort($data, function ($a, $b) use ($sort_by, $order) {
                if (!isset($a[$sort_by]) || !isset($b[$sort_by])) {
                    return 0;
                }

                if ($order === 'asc') {
                    return strtotime($a[$sort_by]) - strtotime($b[$sort_by]);
                } else {
                    return strtotime($b[$sort_by]) - strtotime($a[$sort_by]);
                }
            });

            $final_data = array_merge($final_data, $data);
        }

        $closing_balance = isset($opening_balance->closing_balance) ? $opening_balance->closing_balance : 0;

        return response()->stream(function () use ($final_data, $total_amount, $closing_balance ,$order) {
            // Clear any previous output
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Open the output stream
            $handle = fopen('php://output', 'w');

            // Add the CSV header
            fputcsv($handle, ['Transaction Date', 'Completed Date', 'Txn No', 'Partner Txn No', 'Account No', 'Source', 'Type', 'E-Wallet Acc No', 'Amount', 'Chargers', 'Final Amount', 'Balance', 'Transaction Type']);

            if($order == "desc"){
                $balance = (float)$closing_balance + $total_amount;
            }
            else{
                $balance = (float)$closing_balance + 0;
            }
            // Loop through data and write to CSV
            foreach ($final_data as $row) {

                $f_amount =(float)$row['final_amount'];
                if($order == "asc"){
                    $balance = (float)$balance + $f_amount;
                }

                if ($row['transection_type'] == 1) {
                    $type = "Deposit";
                } elseif ($row['transection_type'] == 2) {
                    $type = "Withdrawal";
                } elseif ($row['transection_type'] == 3) {
                    $type = "Adjustment";
                } elseif ($row['transection_type'] == 4) {
                    $type = "Settlement";
                } elseif ($row['transection_type'] == 5) {
                    $type = "Commission";
                } elseif ($row['transection_type'] == 7) {
                    $type = "Withdrawal Refunded";
                } else {
                    $type = $row['transection_type'];
                }

                fputcsv($handle, [
                    $row['txn_created_at'],
                    $row['updated_at'],
                    $row['transection_id'],
                    $row['partner_transection_id'],
                    $row['sender'],
                    $row['e_wallet_name'],
                    $row['e_wallet_type'],
                    $row['e_wallet_phone_number'],
                    $row['amount'],
                    $row['charge'],
                    number_format($row['final_amount'], 2),
                    number_format($balance, 2),
                    $type,
                ]);
                if($order === "desc"){
                    $balance = (float)$balance - (float)$f_amount;
                }

            }

            // Close the handle
            fclose($handle);
        }, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"transaction_completed_at_log_report.csv\"",
            "Cache-Control" => "no-cache, no-store, must-revalidate",
            "Pragma" => "no-cache",
            "Expires" => "0"
        ]);



        return response()->stream($callback, 200, $headers);
    }
}
