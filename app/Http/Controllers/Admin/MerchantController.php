<?php

namespace App\Http\Controllers\Admin;
use App\Models\Api;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Exports\MerchantReportExport;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
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
}
