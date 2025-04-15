<?php

namespace App\Http\Controllers\Admin;
use App\Models\Api;
use App\Models\Fund;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    public function payment_gateway_report(Request $request)
    {
        $pageTitle = "Payment Gateway Performance Report";
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');
        $partner_id = $request->partner;


        $partners = Api::where('type', 'Admin')->pluck('name', 'id');

        // Query for funds data
        $fundsQuery = Fund::selectRaw('DATE(created_at) as completion_at, api_id, COUNT(*) as fund_count')
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->groupBy(DB::raw('DATE(created_at)'), 'api_id');

        if (!empty($partner_id)) {
            $fundsQuery->where('api_id', $partner_id); // Filter by partner if provided
        }

        $funds = $fundsQuery->get();

        // Query for payments data
        $paymentsQuery = Payment::selectRaw(
            'DATE(completion_at) as completion_date,
             api_id,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND completed_source IS NOT NULL THEN 1 END) as auto_process_count,
             COUNT(CASE WHEN completed_source = ? AND status = ? AND completed_source IS NOT NULL THEN 1 END) as manual_process_count,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 10 THEN 1 END) as time_less_than_10,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 10 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 20 THEN 1 END) as time_between_10_and_20,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 20 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 30 THEN 1 END) as time_between_20_and_30,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 30 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 40 THEN 1 END) as time_between_30_and_40,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 40 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 50 THEN 1 END) as time_between_40_and_50,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 50 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 60 THEN 1 END) as time_between_50_and_60,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 60 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 300 THEN 1 END) as time_between_60_and_5_minutes,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 300 AND TIMESTAMPDIFF(SECOND, created_at, completion_at) <= 600 THEN 1 END) as time_between_5_and_10_minutes,
             COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, completion_at) > 600 THEN 1 END) as time_greater_than_10_minutes',
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
        ->whereNotNull('completion_at')
        ->whereNotNull('created_at')
        ->whereDate('completion_at', '>=', $from_date)
        ->whereDate('completion_at', '<=', $to_date)
        ->groupBy(DB::raw('DATE(completion_at)'), 'api_id');

        if (!empty($partner_id)) {
            $paymentsQuery->where('api_id', $partner_id); // Filter by partner if provided
        }
        $payments = $paymentsQuery->get();

        // Merging both funds and payments data
        $combined = [];

        // Prepare funds data by api_id and date
        foreach ($funds as $fund) {
            $combined[$fund->completion_at][$fund->api_id]['fund_count'] = $fund->fund_count ?? 0;
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
        return view('admin.payment.payment_gateway_report', compact('pageTitle', 'from_date', 'to_date', 'partners', 'combined'));
    }
}
