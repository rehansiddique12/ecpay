<?php

namespace App\Http\Controllers\Partner;
use App\Models\Api;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use App\Models\Fund;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SummaryReportController extends Controller
{
    public function payment_gateway_report(Request $request)
    {
        $user = Auth::guard('partner')->user();
        $main_admin = Api::where('type', 'Admin')->where('api_key', $user->api_key)->first();
        $partnerTimezone = $main_admin->timezone;



        $pageTitle = "Payment Gateway Performance Report";
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');


        $originalTimezone = $partnerTimezone;
        $targetTimezone = env('APP_TIMEZONE', 'Asia/Dhaka');
        $from_date_to_search = Carbon::parse($from_date, $originalTimezone)->setTimezone($targetTimezone);
        $to_date_to_search = Carbon::parse($to_date, $originalTimezone)->setTimezone($targetTimezone);

        $offset = Carbon::now(new CarbonTimeZone($partnerTimezone))->format('P');

        // Query for funds data
        $fundsQuery = Fund::selectRaw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as created_at, api_id, COUNT(*) as fund_count")
            ->where('api_id', $user->id)
            ->where('created_at', '>=', $from_date_to_search)
            ->where('created_at', '<=', $to_date_to_search)
            ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"), 'api_id');




        $funds = $fundsQuery->get();


        // Query for payments data
        $paymentsQuery = Payment::selectRaw(
            "DATE(CONVERT_TZ(created_at, '+06:00', '$offset')) as completion_date,
             api_id,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND completed_source IS NOT NULL THEN 1 END) as auto_process_count,
             COUNT(CASE WHEN completed_source = ? AND status = ? AND completed_source IS NOT NULL THEN 1 END) as manual_process_count,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 10 THEN 1 END) as time_less_than_10,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 10 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 20 THEN 1 END) as time_between_10_and_20,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 20 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 30 THEN 1 END) as time_between_20_and_30,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 30 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 40 THEN 1 END) as time_between_30_and_40,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 40 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 50 THEN 1 END) as time_between_40_and_50,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 50 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 60 THEN 1 END) as time_between_50_and_60,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 60 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 300 THEN 1 END) as time_between_60_and_5_minutes,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 300 AND TIMESTAMPDIFF(SECOND, created_at, created_at) <= 600 THEN 1 END) as time_between_5_and_10_minutes,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 600 THEN 1 END) as time_greater_than_10_minutes",
            [
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete',
                'AdminPanel', 'Complete'
            ]
        )
        ->whereNotNull('created_at')
        ->whereNotNull('created_at')
        ->where('api_id', $user->id)
        ->where('created_at', '>=', $from_date_to_search)
        ->where('created_at', '<=', $to_date_to_search)

        ->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+06:00', '$offset'))"), 'api_id');

        $payments = $paymentsQuery->get();

        // Merging both funds and payments data
        $combined = [];

        // Prepare funds data by api_id and date
        foreach ($funds as $fund) {
            $combined[$fund->created_at][$fund->api_id]['fund_count'] = $fund->fund_count ?? 0;
        }

        // Merge payments data into the combined array
        foreach ($payments as $payment) {
            if (!isset($combined[$payment->completion_date])) {
                $combined[$payment->completion_date] = [];
            }

            if (!isset($combined[$payment->completion_date][$payment->api_id])) {
                $combined[$payment->completion_date][$payment->api_id] = [];
            }

            $combined[$payment->completion_date][$payment->api_id]['auto_process_count'] = $payment->auto_process_count ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['manual_process_count'] = $payment->manual_process_count ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_less_than_10'] = $payment->time_less_than_10 ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_10_and_20'] = $payment->time_between_10_and_20 ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_20_and_30'] = $payment->time_between_20_and_30 ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_30_and_40'] = $payment->time_between_30_and_40 ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_40_and_50'] = $payment->time_between_40_and_50 ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_50_and_60'] = $payment->time_between_50_and_60 ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_60_and_5_minutes'] = $payment->time_between_60_and_5_minutes ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_between_5_and_10_minutes'] = $payment->time_between_5_and_10_minutes ?? 0;
            $combined[$payment->completion_date][$payment->api_id]['time_greater_than_10_minutes'] = $payment->time_greater_than_10_minutes ?? 0;
        }
        // Return the view with the combined data
        return view('partner.summery_reports.payment_gateway_report', compact('pageTitle', 'from_date', 'to_date', 'combined'));
    }
}
