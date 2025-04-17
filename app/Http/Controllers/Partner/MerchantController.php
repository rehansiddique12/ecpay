<?php

namespace App\Http\Controllers\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Exports\MerchantReportExport;
use App\Exports\PartnerMerchantExport;
use App\Exports\PartnerMerchantExportName;
use App\Exports\PartnerMerchantExportMonth;

class MerchantController extends Controller
{
    function report_by_date(Request $request)
    {
        $from_date = date('Y-m-d');
        $results = null;
        $totalCommissionAll = null;

        $user = Auth::guard('partner')->user();
        // dd($user);
        // $apis = Api::where('type', 'Admin')
        //             ->where('parent_id', $user->id)
        //             ->pluck('name', 'id')->toArray();

        // $apis = DB::table('apis')->pluck('name', 'id')->toArray();
        if($request->filled('search_post'))
        {
            $from_date = $request->input('from_date');
        }

        if ($user) {
            $totalCommissionAll = DB::table('partner_commissions')
                                    ->where('status', 1)
                                    ->where('from_id', $user->id)
                                    ->whereDate('created_at', $from_date)
                                    ->sum('charges');
        } else {
            // Optional: Handle case where $user is null
            $totalCommissionAll = 0; // or return error, or log, etc.
        }


        // $apiIds = Api::where('type', 'Admin')
        //     ->where('parent_id', $user->id)
        //     ->pluck('id')
        //     ->toArray();


        $partner_ids = [];

        if ($user) {
            $partner_ids = PartnerCommission::where('from_id', $user->id)
                ->distinct()
                ->pluck('api_id')
                ->toArray();
        }



        // If no partner IDs are found, set an empty collection
        if (empty($partner_ids)) {
            $apiIds = collect();
            $apis = collect();
        } else {
            $apiIds = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                ->pluck('id')
                ->toArray();

                $apis = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                ->pluck('name', 'id')->all();
        }

        $results = collect(); // default empty result

        if ($user && !empty($apiIds)) {
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
                ->where('from_id', $user->id)
                ->whereIn('api_id', $apiIds)
                ->whereDate('created_at', $from_date)
                ->groupBy('api_id')
                ->get();
        }

        if ($user) {
            $userID = $user->id;
        } else {
            // Handle the null case (redirect, error, or fallback)
            $userID = null; // or maybe return redirect()->route('login');
        }

        $pageTitle = "Merchants Summary Report On Date";
        return view('partner.merchant.report_by_date' , compact('pageTitle' , 'from_date' , 'results' , 'apis' , 'totalCommissionAll'));
    }


    public function export_by_date(Request $request)
    {
        $from_date = $request->input('from_date');
        $from_date = str_replace('/', '', $from_date); // Remove any slashes if present

        $user = Auth::guard('partner')->user();
        $userID = $user->id;

        try {
            $sanitizedDate = Carbon::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
        return Excel::download(new PartnerMerchantExport($from_date , $userID), "merchant_report_by_date_{$sanitizedDate}.csv");
    }


    function report_by_name(Request $request)
    {
        $from_date = $request->input('from_date') ??date('Y-m-d');
        $to_date = $request->input('to_date') ??date('Y-m-d');
        $merchant = $request->input('merchant') ?? null;

        $user = Auth::guard('partner')->user();
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
            ->where('from_id' , $user->id)
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
                ->where('from_id' , $user->id)
                ->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date)
                ->groupBy('api_id', DB::raw('DATE(created_at)')) // Group by api_id and the date part of created_at
                ->get();
                // dd($results);
        }

        $partner_ids = PartnerCommission::where('from_id', $user->id)
            ->distinct()
            ->pluck('api_id')
            ->toArray();


        // If no partner IDs are found, set an empty collection
        if (empty($partner_ids)) {
            $apis = collect();
        } else {
            $apis = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                ->pluck('name', 'id')->all();
        }


        // $apis = Api::where('type', 'Admin')
        // ->where('parent_id', $user->id)
        // ->where(function($query) {
        //     $query->where('website', '!=', env('APP_WEBSITE'))
        //         ->orWhereNull('website');
        // })->pluck('name', 'id')->all();
        $userID = $user->id;
        $pageTitle = "Merchant Summary Report Between Dates";
        return view('partner.merchant.report_by_name' , compact('from_date' , 'pageTitle' , 'results' , 'apis' , 'to_date' ,'totalSummary' , 'userID'));
    }

    public function export_by_name(Request $request)
    {
        $from_date = $request->input('from_date');
        $from_date = str_replace('/', '', $from_date);
        $to_date = $request->input('to_date');
        $to_date = str_replace('/', '', $to_date);

        $user = Auth::guard('partner')->user();
        $userID = $user->id;

        $merchant = $request->input('merchant');

        try {
            $sanitizedDate = Carbon::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }
        return Excel::download(new PartnerMerchantExportName($from_date , $to_date,$userID , $merchant), "merchant_report_by_name_{$sanitizedDate}.csv");
    }

}
