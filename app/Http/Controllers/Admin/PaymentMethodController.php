<?php

namespace App\Http\Controllers\Admin;
use App\Models\Api;
use App\Models\Fund;
use App\Models\Gateway;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{

    public function deactivate(Request $request)
    {
        $data = Gateway::where('code', $request->code)->firstOrFail();

        if ($data->status == 1) {
            $data->status = 0;
        } else {
            $data->status = 1;
        }
        $data->save();

        return back()->with('success', 'Updated Successfully.');
    }

    public function payment_gateway_report(Request $request)
{
    $pageTitle = "Payment Gateway Performance Report";
    $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
    $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');
    $partner_id = $request->partner;
    $e_wallet_names = $request->e_wallet_name;
    $transaction_type = $request->transaction_type;

    $partners = Api::where('type', 'Admin')->pluck('name', 'id');

    
    // Query for payments data
    $paymentsQuery = Payment::selectRaw(
        'DATE(created_at) as completion_date,
        COUNT(*) as fund_count,
         api_id,
         e_wallet_name,
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
         COUNT(CASE WHEN completed_source != ? AND status = ? AND TIMESTAMPDIFF(SECOND, created_at, created_at) > 600 THEN 1 END) as time_greater_than_10_minutes',
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
    ->whereDate('created_at', '>=', $from_date)
    ->whereDate('created_at', '<=', $to_date)
    ->groupBy(DB::raw('DATE(created_at)'), 'api_id', 'e_wallet_name');

    if (!empty($partner_id)) {
        $paymentsQuery->where('api_id', $partner_id);
    }

    $payments = $paymentsQuery->get();

    // Merging both funds and payments data
    $combined = [];
    $e_combined = [];

    

    foreach ($payments as $payment) {
        $date = (string) $payment->completion_date;

        if (!isset($combined[$date])) {
            $combined[$date] = [];
            $e_combined[$date] = [];
        }

        if (!isset($e_combined[$date])) {
            $e_combined[$date] = [];
        }

        if (!isset($combined[$date][$payment->api_id])) {
            $combined[$date][$payment->api_id] = [];
        }

        $payment->e_wallet_name = strtolower($payment->e_wallet_name);

        if (!isset($e_combined[$date][$payment->e_wallet_name])) {
            $e_combined[$date][$payment->e_wallet_name] = [];
        }

        $fields = [
            'fund_count',
            'auto_process_count',
            'manual_process_count',
            'time_less_than_10',
            'time_between_10_and_20',
            'time_between_20_and_30',
            'time_between_30_and_40',
            'time_between_40_and_50',
            'time_between_50_and_60',
            'time_between_60_and_5_minutes',
            'time_between_5_and_10_minutes',
            'time_greater_than_10_minutes'
        ];
        
        foreach ($fields as $field) {
            $combined[$date][$payment->api_id][$field] = 
                ($combined[$date][$payment->api_id][$field] ?? 0) + ($payment->$field ?? 0);
        }


        $e_combined[$date][$payment->e_wallet_name]['fund_count'] = $payment->fund_count ?? 0;
        $e_combined[$date][$payment->e_wallet_name]['auto_process_count'] = $payment->auto_process_count ?? 0;
        $e_combined[$date][$payment->e_wallet_name]['manual_process_count'] = $payment->manual_process_count ?? 0;
    }
    

    return view('admin.payment.payment_gateway_report', compact('pageTitle', 'from_date', 'to_date', 'partners', 'combined','e_combined'));
}

}
