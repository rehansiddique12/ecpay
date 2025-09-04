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
        $pageTitle = __('reports.payment_gateway_performance_report');

        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');
        $partner_id = $request->partner;
        $e_wallet_names = $request->e_wallet_name;
        $transaction_type = $request->transaction_type;

        $partners = Api::where('type', 'Admin')->pluck('name', 'id');

        // dd($partners);

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
        ->whereDate('created_at', '<=', $to_date);

        // ✅ Apply search filters
        if (!empty($partner_id)) {
            $paymentsQuery->where('api_id', $partner_id);
        }

        if (!empty($transaction_type)) {
            $paymentsQuery->where('transaction_type', $transaction_type);
        }

        if (!empty($e_wallet_names) && is_array($e_wallet_names)) {
            $paymentsQuery->whereIn('e_wallet_name', $e_wallet_names);
        }

        // Group by date, partner, and wallet
        $paymentsQuery->groupBy(DB::raw('DATE(created_at)'), 'api_id', 'e_wallet_name');

        $payments = $paymentsQuery->get();

        $combined = [];
        $e_combined = [];

        foreach ($payments as $payment) {
            $date = (string) $payment->completion_date;

            if (!isset($combined[$date])) {
                $combined[$date] = [];
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

            $e_combined[$date][$payment->e_wallet_name]['fund_count'] =
                isset($e_combined[$date][$payment->e_wallet_name]['fund_count'])
                    ? $e_combined[$date][$payment->e_wallet_name]['fund_count'] + ($payment->fund_count ?? 0)
                    : ($payment->fund_count ?? 0);

            $e_combined[$date][$payment->e_wallet_name]['auto_process_count'] =
                isset($e_combined[$date][$payment->e_wallet_name]['auto_process_count'])
                    ? $e_combined[$date][$payment->e_wallet_name]['auto_process_count'] + ($payment->auto_process_count ?? 0)
                    : ($payment->auto_process_count ?? 0);

            $e_combined[$date][$payment->e_wallet_name]['manual_process_count'] =
                isset($e_combined[$date][$payment->e_wallet_name]['manual_process_count'])
                    ? $e_combined[$date][$payment->e_wallet_name]['manual_process_count'] + ($payment->manual_process_count ?? 0)
                    : ($payment->manual_process_count ?? 0);

        }

        // dd($e_combined);

        return view('admin.payment.payment_gateway_report', compact(
            'pageTitle',
            'from_date',
            'to_date',
            'partners',
            'combined',
            'e_combined'
        ));
    }

    public function payment_gateway_report_detail($id, $from_date, $to_date)
    {
        $pageTitle = "Payment Gateway Performance Report Detail";
        $partner_id = $id;
        $partners = Api::where('type', 'Admin')->where('id',$id)->pluck('name', 'id');

        $partner = $partners[$id];

        $combined = [];
        $time_slots = [];

        $start = strtotime($from_date . ' 00:00:00');
        $end = strtotime($to_date . ' 23:59:59');

        while ($start <= $end) {
            $from_time = date('Y-m-d H:i:s', $start);
            $to_time = date('Y-m-d H:i:s', $start + 1799);
            $time_slots[] = ['from' => $from_time, 'to' => $to_time];
            $start += 1800;
        }

        // Fetch all fund counts in one query
        $funds = Payment::selectRaw('COUNT(*) as fund_count, api_id, DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as slot')
            ->where('api_id', $partner_id)
            ->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59'])
            ->groupBy('api_id', 'slot')
            ->get()
            ->keyBy('slot');

        // Fetch all payments in one query
        $payments = Payment::selectRaw('
            api_id,
            DATE_FORMAT(trans_complete_date, "%Y-%m-%d %H:%i") as slot,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" THEN 1 END) as auto_process_count,
            COUNT(CASE WHEN completed_source = "AdminPanel" AND status = "Complete" THEN 1 END) as manual_process_count,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) <= 10 THEN 1 END) as time_less_than_10,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 11 AND 20 THEN 1 END) as time_between_10_and_20,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 21 AND 30 THEN 1 END) as time_between_20_and_30,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 31 AND 40 THEN 1 END) as time_between_30_and_40,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 41 AND 50 THEN 1 END) as time_between_40_and_50,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 51 AND 60 THEN 1 END) as time_between_50_and_60,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 61 AND 300 THEN 1 END) as time_between_60_and_5_minutes,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) BETWEEN 301 AND 600 THEN 1 END) as time_between_5_and_10_minutes,
            COUNT(CASE WHEN completed_source != "AdminPanel" AND status = "Complete" AND TIMESTAMPDIFF(SECOND, created_at, trans_complete_date) > 600 THEN 1 END) as time_greater_than_10_minutes
        ')
        ->where('api_id', $partner_id)
        ->whereBetween('trans_complete_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59'])
        ->groupBy('api_id', 'slot')
        ->get()
        ->keyBy('slot');

        foreach ($time_slots as $slot) {
            $time_range = date('H:i:s', strtotime($slot['from'])) . " To " . date('H:i:s', strtotime($slot['to']));

            if (!isset($combined[$time_range][$partner_id])) {
                $combined[$time_range][$partner_id] = [
                    'fund_count' => 0,
                    'auto_process_count' => 0,
                    'manual_process_count' => 0,
                    'time_less_than_10' => 0,
                    'time_between_10_and_20' => 0,
                    'time_between_20_and_30' => 0,
                    'time_between_30_and_40' => 0,
                    'time_between_40_and_50' => 0,
                    'time_between_50_and_60' => 0,
                    'time_between_60_and_5_minutes' => 0,
                    'time_between_5_and_10_minutes' => 0,
                    'time_greater_than_10_minutes' => 0,
                ];
            }

            // Sum all funds within the time slot range
            foreach ($funds as $fund_key => $fund) {
                $fund_time = strtotime($fund_key);
                $slot_start = strtotime($slot['from']);
                $slot_end = strtotime($slot['to']);

                if ($fund_time >= $slot_start && $fund_time < $slot_end) {
                    $combined[$time_range][$partner_id]['fund_count'] += $fund->fund_count ?? 0;
                }
            }

            // Sum all payments within the time slot range
            foreach ($payments as $payment_key => $payment) {
                $payment_time = strtotime($payment_key);
                $slot_start = strtotime($slot['from']);
                $slot_end = strtotime($slot['to']);

                if ($payment_time >= $slot_start && $payment_time < $slot_end) {
                    $combined[$time_range][$partner_id]['auto_process_count'] += $payment->auto_process_count ?? 0;
                    $combined[$time_range][$partner_id]['manual_process_count'] += $payment->manual_process_count ?? 0;
                    $combined[$time_range][$partner_id]['time_less_than_10'] += $payment->time_less_than_10 ?? 0;
                    $combined[$time_range][$partner_id]['time_between_10_and_20'] += $payment->time_between_10_and_20 ?? 0;
                    $combined[$time_range][$partner_id]['time_between_20_and_30'] += $payment->time_between_20_and_30 ?? 0;
                    $combined[$time_range][$partner_id]['time_between_30_and_40'] += $payment->time_between_30_and_40 ?? 0;
                    $combined[$time_range][$partner_id]['time_between_40_and_50'] += $payment->time_between_40_and_50 ?? 0;
                    $combined[$time_range][$partner_id]['time_between_50_and_60'] += $payment->time_between_50_and_60 ?? 0;
                    $combined[$time_range][$partner_id]['time_between_60_and_5_minutes'] += $payment->time_between_60_and_5_minutes ?? 0;
                    $combined[$time_range][$partner_id]['time_between_5_and_10_minutes'] += $payment->time_between_5_and_10_minutes ?? 0;
                    $combined[$time_range][$partner_id]['time_greater_than_10_minutes'] += $payment->time_greater_than_10_minutes ?? 0;
                }
            }
        }


        return view('admin.payment.payment_gateway_report_detail', compact('pageTitle', 'from_date', 'to_date', 'partners', 'combined','partner'));
    }


}
