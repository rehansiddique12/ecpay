<?php

namespace App\Http\Controllers\Admin;
use App\Models\Api;
use App\Models\Log;
use App\Models\ApiLog;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\CCategory;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ParentCommission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantReportExport;

class MerchantController extends Controller
{
    public function profile($id)
{
    $pageTitle = "Merchants Profile";
    $data = Api::findOrFail($id);

    $MCommissions= Commission::where('category_id',$data->category_id)->get();
    $ids = $MCommissions->pluck('id')->toArray();

    $PartnerCommission= ParentCommission::with('partner')->where('user_id',$id)->whereIn('commission_id',$ids)->get();

    $categories = CCategory::where('status',1)->get();
    $payments = Payment::selectRaw(
        'DATE(created_at) as completion_date,
         api_id,
         COUNT(CASE WHEN completed_source != ? AND status = ? AND completed_source IS NOT NULL THEN 1 END) as auto_process_count,
         COUNT(CASE WHEN status = ? AND completed_source IS NOT NULL THEN 1 END) as fund_count',
        ['AdminPanel', 'Complete', 'Complete']
    )
    ->whereNotNull('created_at')
    ->where('api_id', $id)
    ->groupBy(DB::raw('DATE(created_at)'), 'api_id')
    ->get();

    $combined = [];
    $totalAutoProcess = 0;
    $totalFundCount = 0;

    foreach ($payments as $payment) {
        $combined[$payment->completion_date][$payment->api_id] = [
            'auto_process_count' => $payment->auto_process_count ?? 0,
            'fund_count' => $payment->fund_count ?? 0,
        ];

        $totalAutoProcess += $payment->auto_process_count ?? 0;
        $totalFundCount += $payment->fund_count ?? 0;
    }

    $total_deposit = $totalFundCount > 0
        ? round(($totalAutoProcess / $totalFundCount) * 100, 2)
        : 0;

    return view('admin.merchant.merchant-profile', compact(
        'data',
        'pageTitle',
        'combined',
        'total_deposit',
        'categories',
        'id',
        'PartnerCommission',
        'MCommissions'
    ));
}

public function mechantlogs($id){
    $pageTitle = "Partner Balance Logs";
    $data = Api::findOrFail($id);
    $live_balance_logs = Log::where('partner_id', $id)->whereDate('created_at', Carbon::today())->with('api')->orderBy('logs.created_at', 'desc')->get();
    $filter_data = [];
    foreach ($live_balance_logs as $key => $item) {
        $filter_data[$key]['id'] =  $item->id;
        $filter_data[$key]['partner'] =  $item->api->name;
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
                }
            }elseif($item->transection_type==3){
                $ApiTransaction = ApiTransaction::where('id', $item->transection_id)->first();
                if($ApiTransaction){
                    $filter_data[$key]['amount'] =  $ApiTransaction->amount;
                    $filter_data[$key]['charge'] =  $ApiTransaction->charges;
                    $filter_data[$key]['e_wallet_name'] =  $ApiTransaction->source;
                    $filter_data[$key]['txn_id'] =  $ApiTransaction->txn;
                    $filter_data[$key]['txn_created_at'] =  $ApiTransaction->created_at;
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
                }
            }

    }
    $deposit_logs = Payment::where('status', '!=', 'initiate')->where('api_id',$id)->orderBy('id', 'DESC')->with('user', 'gateway','txn_record')->paginate(config('basic.paginate'));
    $withrawl_logs = Payout::where('status', '!=', 'initiate')->where('api_id',$id)->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));
    $payments = Payment::selectRaw(
        'DATE(created_at) as completion_date,
         api_id,
         COUNT(CASE WHEN completed_source != ? AND status = ? AND completed_source IS NOT NULL THEN 1 END) as auto_process_count,
         COUNT(CASE WHEN status = ? AND completed_source IS NOT NULL THEN 1 END) as fund_count',
        ['AdminPanel', 'Complete', 'Complete']
    )
    ->whereNotNull('created_at')
    ->where('api_id', $id)
    ->groupBy(DB::raw('DATE(created_at)'), 'api_id')
    ->get();

    $combined = [];
    $totalAutoProcess = 0;
    $totalFundCount = 0;

    foreach ($payments as $payment) {
        $combined[$payment->completion_date][$payment->api_id] = [
            'auto_process_count' => $payment->auto_process_count ?? 0,
            'fund_count' => $payment->fund_count ?? 0,
        ];

        $totalAutoProcess += $payment->auto_process_count ?? 0;
        $totalFundCount += $payment->fund_count ?? 0;
    }

    $total_deposit = $totalFundCount > 0
        ? round(($totalAutoProcess / $totalFundCount) * 100, 2)
        : 0;

    return view('admin.merchant.merchant-log', compact(
        'data',
        'pageTitle',
        'filter_data',
        'deposit_logs',
        'withrawl_logs',
        'total_deposit'
    ));

}



    function report_by_date(Request $request)
    {
        $from_date = date('Y-m-d');
        // dd($from_date);
        $results = null;
        $totalCommissionAll = null;

        $apis = DB::table('apis')->pluck('name', 'id')->toArray();
        if($request->filled('search_post'))
        {
            $from_date = $request->input('from_date');
        }

        $totalCommissionAll = DB::table('partner_commissions')
                        ->where('status', 1)
                        ->whereDate('created_at', $from_date)
                        ->sum('charges');

        $results = DB::table('partner_commissions')
        ->select(
            'api_id',
            DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
            DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_charges_deposit'),
            DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
            DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_charges_withdrawal'),
            DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'),
            DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'),
            DB::raw('SUM(charges) as total_commission')
        )
        ->where('status', 1)
        ->whereDate('created_at', $from_date)
        ->groupBy('api_id')
        ->get();

        $pageTitle = "Merchants Summary Report On Date";
        return view('admin.merchant.report_by_date' , compact('pageTitle' , 'from_date' , 'results' , 'apis' , 'totalCommissionAll'));
    }
    public function export_by_date($from_date)
    {
        $from_date = str_replace('/', '', $from_date); // Remove any slashes if present

        try {
            $sanitizedDate = Carbon::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
        return Excel::download(new MerchantReportExport($from_date), "merchant_report_by_date_{$sanitizedDate}.csv");
    }


    function report_by_name(Request $request)
    {
        $from_date = $request->input('from_date') ??date('Y-m-d');
        $to_date = $request->input('to_date') ??date('Y-m-d');
        $merchant = $request->input('merchant') ?? null;
        $results = null;
        $totalSummary = null;
        if ($request->filled('search') && !$merchant) {
            // Flash error message and return back
            session()->flash('error', 'Please select a merchant to create the report.');
            return redirect()->back();
        }

        // dd($request->all());

        if ($merchant) {

            $totalSummary = DB::table('partner_commissions')
            ->select(
                'api_id',
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'), // Total deposits
                DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_deposit_commission'), // Deposit commissions
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'), // Total withdrawals
                DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_withdrawal_commission'), // Withdrawal commissions
                DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'), // Deposit transaction count
                DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'), // Withdrawal transaction count
                DB::raw('SUM(charges) as total_commission') // Total commission (deposit + withdrawal)
            )
            ->where('status', 1)
            ->where('api_id', $merchant)
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->groupBy('api_id')
            ->first();

            // dd($totalSummary);

            $results = DB::table('partner_commissions')
                ->select(
                    'api_id',
                    DB::raw('DATE(created_at) as date'), // Extract only the date part of created_at
                    DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
                    DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_charges_deposit'),
                    DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
                    DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_charges_withdrawal'),
                    DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'),
                    DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'),
                    DB::raw('SUM(charges) as total_commission')
                )
                ->where('status', 1)
                ->where('api_id', $merchant)
                ->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date)
                ->groupBy('api_id', DB::raw('DATE(created_at)')) // Group by api_id and the date part of created_at
                ->get();
                // dd($results);
        }


        $apis = Api::where('type', 'Admin')
        ->where(function($query) {
            $query->where('website', '!=', env('APP_WEBSITE'))
                ->orWhereNull('website');
        })->pluck('name', 'id')->all();

        $pageTitle = "Merchant Summary Report Between Dates";
        return view('admin.merchant.report_by_name' , compact('from_date' , 'pageTitle' , 'results' , 'apis' , 'to_date' ,'totalSummary'));
    }


    public function export_by_name($from_date)
    {
        $from_date = str_replace('/', '', $from_date); // Remove any slashes if present

        try {
            $sanitizedDate = Carbon::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
        return Excel::download(new MerchantReportExport($from_date), "merchant_report_by_date_{$sanitizedDate}.csv");
    }


    function report_by_month(Request $request)
    {
        $from_date = $request->input('searchYear') ?? date('Y');
        $merchant = $request->input('merchant') ?? null;
        $results = null;

        $query = DB::table('partner_commissions')
        ->select(
            DB::raw('MONTH(created_at) as month'),
            'api_id',
            DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
            DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
            DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_charges_deposit'),
            DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_charges_withdrawal'),
            DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'),
            DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'),
            DB::raw('SUM(charges) as total_commission')
        )
        ->where('status', 1)
        ->whereYear('created_at', $from_date) // Filter by the selected year
        ->groupBy(DB::raw('MONTH(created_at)'), 'api_id') // Group by month and api_id
        ->orderBy(DB::raw('MONTH(created_at)')); // Sort by month in ascending order

        // Filter by specific api_id if provided
        if (!empty($merchant)) {
            $query->where('api_id', $merchant);
        }
        // Get the results
        $results = $query->get();

        // dd($results);
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];


        $apis = Api::where('type', 'Admin')
        ->where(function($query) {
            $query->where('website', '!=', env('APP_WEBSITE'))
                ->orWhereNull('website');
        })->pluck('name', 'id')->all();
        $pageTitle = "Merchant Summary Report of Month";
        return view('admin.merchant.report_by_month' , compact('pageTitle' , 'from_date' , 'apis' , 'results' , 'months'));
    }


    public function export_by_month($year)
    {
        try {
            // Validate that the input is a valid year
            if (!is_numeric($year) || strlen($year) !== 4) {
                return response()->json(['error' => 'Invalid year format. Please provide a valid year (YYYY).'], 400);
            }

            // Create a sanitized year string
            $sanitizedYear = (string)$year;

            return Excel::download(new MerchantReportExport($sanitizedYear), "merchant_report_by_month_{$sanitizedYear}.csv");
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the export.'], 400);
        }
    }


public function fetchActivityLogs(Request $request)
{
    $logs = ApiLog::whereJsonContains('request_payload->partner_transection_id', $request->partner_transaction_id)->get();
    return response()->json([
        'data' => $logs
    ]);
}
}
