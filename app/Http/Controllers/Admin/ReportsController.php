<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Log;
use App\Models\Payout;
use App\Models\Payment;
use Carbon\CarbonPeriod;
use App\Models\Settlement;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ApiTransaction;
use App\Models\EWalletAccount;
use App\Models\EWalletTransfer;
use App\Models\PartnerCommission;
use App\Models\EWalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\DailyEWalletSummary;
use App\Models\DailyPartnerSummary;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    public function live_ewallet_balance()
    {
        $data = EWalletAccount::orderBy('e_wallet_name', 'asc')->get();
        $sumBalance = $data->sum('balance');
        $sumDailySent = $data->sum('daily_sent');
        $sumDailyReceived = $data->sum('daily_received');
        // dd($sumDailyReceived);
        $pageTitle = __('reports.live_ewallet_balance');
        return view('admin.reports.live_ewallet_balance', compact('pageTitle', 'data', 'sumBalance', 'sumDailySent', 'sumDailyReceived'));
    }

    public function daily_ewallet_summary(Request $request)
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $defaultDate = $now->toDateString();

        $from_date = $request->filled('from_date') ? $request->from_date : $defaultDate . ' 00:00';
        $to_date = $request->filled('to_date') ? $request->to_date : $now->toDateTimeString();

        $distinctWalletNames = EWalletAccount::select('e_wallet_name')->distinct()->pluck('e_wallet_name');

        $carbonFrom = Carbon::parse($from_date)->startOfDay();
        $carbonTo = Carbon::parse($to_date)->endOfDay();
        $period = CarbonPeriod::create($carbonFrom, $carbonTo);

        // Get all eWallet accounts
        $EWalletAccounts = EWalletAccount::when($request->filled('e_wallet_name'), function ($query) use ($request) {
            $query->where('e_wallet_name', $request->e_wallet_name);
            })
            ->when($request->filled('account_no'), function ($query) use ($request) {
                $query->where('account_no', $request->account_no);
            })
            ->get();
        $accountIds = $EWalletAccounts->pluck('id');
        $eWalletNames = $EWalletAccounts->pluck('e_wallet_name');
        $accountNumbers = $EWalletAccounts->pluck('account_no');

        // Last available closing balance before start date
        $previousBalances = DailyEWalletSummary::whereIn('e_wallet_id', $accountIds)
            ->whereDate('created_at', '<', $carbonFrom->toDateString())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('e_wallet_id')
            ->map(function ($records) {
                return $records->first(); // last closing balance before period
            });

        // DEPOSITS
        $deposits_pre = Payment::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

        // WITHDRAWALS
        $withdrawals_pre = Payout::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

        // TRANSFER IN
        $transfersIn_pre = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('to_account_no', $accountNumbers)
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet, to_account_no, SUM(amount) as total')
            ->groupByRaw('e_wallet, to_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->to_account_no;
            });

        // TRANSFER OUT
        $transfersOut_pre = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('from_account_no', $accountNumbers)
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet, from_account_no, SUM(amount) as total')
            ->groupByRaw('e_wallet, from_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->from_account_no;
            });

        // === Preload All Transactions with Grouping ===

        // DEPOSITS
        $deposits = Payment::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number . '|' . $item->date;
            });

        // WITHDRAWALS
        $withdrawals = Payout::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number . '|' . $item->date;
            });

        // TRANSFER IN
        $transfersIn = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('to_account_no', $accountNumbers)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet, to_account_no, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet, to_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->to_account_no . '|' . $item->date;
            });

        // TRANSFER OUT
        $transfersOut = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('from_account_no', $accountNumbers)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet, from_account_no, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet, from_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->from_account_no . '|' . $item->date;
            });

        // === Prepare Data ===
        $data = [];

        foreach ($EWalletAccounts as $account) {
            $balance = $previousBalances[$account->id]->closing_balance ?? 0.00;

            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                $key = $account->e_wallet_name . '|' . $account->account_no . '|' . $dateStr;
                $key_pre = $account->e_wallet_name . '|' . $account->account_no;

                if ($carbonFrom->toDateString() === $dateStr) {
                    $deposit_pre = $deposits[$key_pre][0]->total ?? 0.00;
                    $withdrawal_pre = $withdrawals[$key_pre][0]->total ?? 0.00;
                    $in_pre = $transfersIn[$key_pre][0]->total ?? 0.00;
                    $out_pre = $transfersOut[$key_pre][0]->total ?? 0.00;
                    $total_pre = $deposit_pre - $withdrawal_pre + $in_pre - $out_pre;
                    $balance = $balance + $total_pre;
                }

                $deposit = $deposits[$key][0]->total ?? 0.00;
                $withdrawal = $withdrawals[$key][0]->total ?? 0.00;
                $in = $transfersIn[$key][0]->total ?? 0.00;
                $out = $transfersOut[$key][0]->total ?? 0.00;

                if ($deposit > 0 || $withdrawal > 0 || $in > 0 || $out > 0) {
                    $closing = $balance + $deposit - $withdrawal + $in - $out;

                    $data[$dateStr][] = [
                        'e_wallet_name' => $account->e_wallet_name,
                        'account_no' => $account->account_no,
                        'date' => $dateStr,
                        'opening_balance' => $balance,
                        'total_deposit' => $deposit,
                        'total_withdrawal' => $withdrawal,
                        'transfer_in' => $in,
                        'transfer_out' => $out,
                        'closing_balance' => $closing,
                    ];

                    $balance = $closing; // update for next date
                }
            }
        }

        $e_wallet_name = $request->e_wallet_name;
        $account_no = $request->account_no;
        $pageTitle = __('reports.daily_ewallet_summary');
        return view('admin.reports.daily_ewallet_summary', compact('pageTitle', 'from_date', 'to_date', 'data', 'EWalletAccounts', 'distinctWalletNames','e_wallet_name','account_no'));
    }

    public function daily_ewallet_summaryOLD3(Request $request)
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $defaultDate = $now->toDateString();

        $from_date = $request->filled('from_date') ? $request->from_date : $defaultDate . ' 00:00';
        $to_date = $request->filled('to_date') ? $request->to_date : $now->toDateTimeString();

        $distinctWalletNames = EWalletAccount::select('e_wallet_name')->distinct()->pluck('e_wallet_name');

        $carbonFrom = Carbon::parse($from_date)->startOfDay();
        $carbonTo = Carbon::parse($to_date)->endOfDay();
        $period = CarbonPeriod::create($carbonFrom, $carbonTo);

        // Get all eWallet accounts
        $EWalletAccounts = EWalletAccount::get();
        $accountIds = $EWalletAccounts->pluck('id');
        $eWalletNames = $EWalletAccounts->pluck('e_wallet_name');
        $accountNumbers = $EWalletAccounts->pluck('account_no');

        // Last available closing balance before start date
        $previousBalances = DailyEWalletSummary::whereIn('e_wallet_id', $accountIds)
            ->whereDate('created_at', '<', $carbonFrom->toDateString())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('e_wallet_id')
            ->map(function ($records) {
                return $records->first(); // last closing balance before period
            });

            // DEPOSITS
            $deposits_pre = Payment::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

            // WITHDRAWALS
            $withdrawals_pre = Payout::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

            // TRANSFER IN
            $transfersIn_pre = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('to_account_no', $accountNumbers)
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet, to_account_no, SUM(amount) as total')
            ->groupByRaw('e_wallet, to_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->to_account_no;
            });

            // TRANSFER OUT
            $transfersOut_pre = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('from_account_no', $accountNumbers)
            ->whereBetween('created_at', [$carbonFrom, $from_date])
            ->selectRaw('e_wallet, from_account_no, SUM(amount) as total')
            ->groupByRaw('e_wallet, from_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->from_account_no;
            });

        // === Preload All Transactions with Grouping ===

        // DEPOSITS
        $deposits = Payment::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number . '|' . $item->date;
            });

        // WITHDRAWALS
        $withdrawals = Payout::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet_name, e_wallet_phone_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number . '|' . $item->date;
            });

        // TRANSFER IN
        $transfersIn = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('to_account_no', $accountNumbers)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet, to_account_no, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet, to_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->to_account_no . '|' . $item->date;
            });

        // TRANSFER OUT
        $transfersOut = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('from_account_no', $accountNumbers)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('DATE(created_at) as date, e_wallet, from_account_no, SUM(amount) as total')
            ->groupByRaw('DATE(created_at), e_wallet, from_account_no')
            ->get()
            ->groupBy(function ($item) {
                return $item->e_wallet . '|' . $item->from_account_no . '|' . $item->date;
            });

            

        // === Prepare Data ===
        $data = [];

        foreach ($EWalletAccounts as $account) {
            $balance = $previousBalances[$account->id]->closing_balance ?? 0.00;

            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                $key = $account->e_wallet_name . '|' . $account->account_no . '|' . $dateStr;
                $key_pre = $account->e_wallet_name . '|' . $account->account_no;

                if ($carbonFrom->toDateString() === $dateStr) {
                    $deposit_pre = $deposits[$key_pre][0]->total ?? 0.00;
                    $withdrawal_pre = $withdrawals[$key_pre][0]->total ?? 0.00;
                    $in_pre = $transfersIn[$key_pre][0]->total ?? 0.00;
                    $out_pre = $transfersOut[$key_pre][0]->total ?? 0.00;
                    $total_pre = $deposit_pre - $withdrawal_pre + $in_pre - $out_pre;
                    $balance = $balance + $total_pre;
                }

                

                $deposit = $deposits[$key][0]->total ?? 0.00;
                $withdrawal = $withdrawals[$key][0]->total ?? 0.00;
                $in = $transfersIn[$key][0]->total ?? 0.00;
                $out = $transfersOut[$key][0]->total ?? 0.00;

                if ($deposit > 0 || $withdrawal > 0 || $in > 0 || $out > 0) {
                    $closing = $balance + $deposit - $withdrawal + $in - $out;

                    $data[$dateStr][] = [
                        'e_wallet_name' => $account->e_wallet_name,
                        'account_no' => $account->account_no,
                        'date' => $dateStr,
                        'opening_balance' => $balance,
                        'total_deposit' => $deposit,
                        'total_withdrawal' => $withdrawal,
                        'transfer_in' => $in,
                        'transfer_out' => $out,
                        'closing_balance' => $closing,
                    ];

                    $balance = $closing; // update for next date
                }
            }
        }


        // dd($data);

        $e_wallet_name = $request->e_wallet_name;
        $account_no = $request->account_no;
        $pageTitle = __('reports.daily_ewallet_summary');
        return view('admin.reports.daily_ewallet_summary', compact('pageTitle', 'from_date', 'to_date', 'data', 'EWalletAccounts', 'distinctWalletNames','e_wallet_name','account_no'));
    }

    public function daily_ewallet_summary_old2(Request $request)
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->toDateString();
        $oneDayBefore = $now->subDay()->toDateString();

        $from_date = date('Y-m-d')." 00:00";
        $to_date = date('Y-m-d H:i');

        if ($request->filled('from_date')) {
            $from_date = $request->from_date;  //2025-06-27T00:00
        }
        

        
        if ($request->filled('to_date')) {
            $to_date = $request->to_date; ////2025-06-28T00:00
        }

        $carbonDate = Carbon::parse($from_date);
        $date = $carbonDate->toDateString();
        $oneDayBefore = $carbonDate->copy()->subDay()->toDateString();

        // Get all wallet accounts with pagination
        $EWalletAccounts = EWalletAccount::get();
        $accountIds = $EWalletAccounts->pluck('id');
        $eWalletNames = $EWalletAccounts->pluck('e_wallet_name');
        $accountNumbers = $EWalletAccounts->pluck('account_no');

        // Preload previous day's closing balances
        $previousBalances = DailyEWalletSummary::whereIn('e_wallet_id', $accountIds)
            ->whereDate('created_at', $oneDayBefore)
            ->get()
            ->keyBy('e_wallet_id');

        // Preload deposits
        $deposits = Payment::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupBy('e_wallet_name', 'e_wallet_phone_number')
            ->get()
            ->keyBy(function($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

        // Preload withdrawals
        $withdrawals = Payout::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupBy('e_wallet_name', 'e_wallet_phone_number')
            ->get()
            ->keyBy(function($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

        // Preload transfers in
        $transfersIn = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('to_account_no', $accountNumbers)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('e_wallet, to_account_no, SUM(amount) as total')
            ->groupBy('e_wallet', 'to_account_no')
            ->get()
            ->keyBy(function($item) {
                return $item->e_wallet . '|' . $item->to_account_no;
            });

        // Preload transfers out
        $transfersOut = EWalletTransfer::whereIn('e_wallet', $eWalletNames)
            ->whereIn('from_account_no', $accountNumbers)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw('e_wallet, from_account_no, SUM(amount) as total')
            ->groupBy('e_wallet', 'from_account_no')
            ->get()
            ->keyBy(function($item) {
                return $item->e_wallet . '|' . $item->from_account_no;
            });

        // Build the data array
        $data = [];
        foreach ($EWalletAccounts as $key => $account) {
            $accountKey = $account->e_wallet_name . '|' . $account->account_no;

            $openingBalance = $previousBalances[$account->id]->closing_balance ?? 0.00;
            $totalDeposit = $deposits[$accountKey]->total ?? 0.00;
            $totalWithdrawal = $withdrawals[$accountKey]->total ?? 0.00;
            $transferIn = $transfersIn[$accountKey]->total ?? 0.00;
            $transferOut = $transfersOut[$accountKey]->total ?? 0.00;

            if($totalDeposit>0 || $totalWithdrawal>0 || $transferIn>0 || $transferOut>0){

                

                $data[$key] = [
                    'e_wallet_name' => $account->e_wallet_name,
                    'account_no' => $account->account_no,
                    'opening_balance' => $openingBalance,
                    'total_deposit' => $totalDeposit,
                    'total_withdrawal' => $totalWithdrawal,
                    'transfer_in' => $transferIn,
                    'transfer_out' => $transferOut,
                    'closing_balance' => $openingBalance + $totalDeposit - $totalWithdrawal + $transferIn - $transferOut
                ];

            }
        }

        $pageTitle = __('reports.daily_ewallet_summary');
        return view('admin.reports.daily_ewallet_summary', compact('pageTitle', 'from_date', 'to_date', 'data', 'EWalletAccounts'));
    }


    public function daily_ewallet_summary_old(Request $request)
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->toDateString();
        $oneDayBefore = $now->subDay()->toDateString();

        if ($request->filled('date')) {
            $date = $request->date;
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $oneDayBefore = $carbonDate->subDay()->toDateString();
        }

        // Get all wallet accounts with pagination
        $EWalletAccounts = EWalletAccount::paginate(20);
        $accountIds = $EWalletAccounts->pluck('id');
        $eWalletNames = $EWalletAccounts->pluck('e_wallet_name');
        $accountNumbers = $EWalletAccounts->pluck('account_no');

        // Preload previous day's closing balances
        $previousBalances = DailyEWalletSummary::whereIn('e_wallet_id', $accountIds)
            ->whereDate('created_at', $oneDayBefore)
            ->get()
            ->keyBy('e_wallet_id');

        // Preload deposits
        $deposits = Payment::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupBy('e_wallet_name', 'e_wallet_phone_number')
            ->get()
            ->keyBy(function($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

        // Preload withdrawals
        $withdrawals = Payout::whereIn('e_wallet_name', $eWalletNames)
            ->whereIn('e_wallet_phone_number', $accountNumbers)
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('e_wallet_name, e_wallet_phone_number, SUM(amount) as total')
            ->groupBy('e_wallet_name', 'e_wallet_phone_number')
            ->get()
            ->keyBy(function($item) {
                return $item->e_wallet_name . '|' . $item->e_wallet_phone_number;
            });

        // Preload transfers in
        $transfersIn = EWalletTransaction::whereIn('to_e_wallet', $eWalletNames)
            ->whereIn('to_account_no', $accountNumbers)
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('to_e_wallet, to_account_no, SUM(amount) as total')
            ->groupBy('to_e_wallet', 'to_account_no')
            ->get()
            ->keyBy(function($item) {
                return $item->to_e_wallet . '|' . $item->to_account_no;
            });

        // Preload transfers out
        $transfersOut = EWalletTransaction::whereIn('from_e_wallet', $eWalletNames)
            ->whereIn('from_account_no', $accountNumbers)
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('from_e_wallet, from_account_no, SUM(amount) as total')
            ->groupBy('from_e_wallet', 'from_account_no')
            ->get()
            ->keyBy(function($item) {
                return $item->from_e_wallet . '|' . $item->from_account_no;
            });

        // Build the data array
        $data = [];
        foreach ($EWalletAccounts as $key => $account) {
            $accountKey = $account->e_wallet_name . '|' . $account->account_no;

            $openingBalance = $previousBalances[$account->id]->closing_balance ?? 0.00;
            $totalDeposit = $deposits[$accountKey]->total ?? 0.00;
            $totalWithdrawal = $withdrawals[$accountKey]->total ?? 0.00;
            $transferIn = $transfersIn[$accountKey]->total ?? 0.00;
            $transferOut = $transfersOut[$accountKey]->total ?? 0.00;

            if($totalDeposit>0 || $totalWithdrawal>0 || $transferIn>0 || $transferOut>0){

            

                $data[$key] = [
                    'e_wallet_name' => $account->e_wallet_name,
                    'account_no' => $account->account_no,
                    'opening_balance' => $openingBalance,
                    'total_deposit' => $totalDeposit,
                    'total_withdrawal' => $totalWithdrawal,
                    'transfer_in' => $transferIn,
                    'transfer_out' => $transferOut,
                    'closing_balance' => $openingBalance + $totalDeposit - $totalWithdrawal + $transferIn - $transferOut
                ];

            }
        }

        $e_wallet_name = $request->e_wallet_name;
        $account_no = $request->account_no;

        $pageTitle = __('reports.daily_ewallet_summary');
        return view('admin.reports.daily_ewallet_summary', compact('pageTitle', 'date', 'data', 'EWalletAccounts'));
    }

    public function smsLogs(Request $request)
    {
        $pageTitle = 'SMS Logs';

        // for the dropdown filter
        $distinctWalletNames = DB::table('sms_logs')->distinct()->pluck('e_wallet_name');

        // query with filters
        $logs = DB::table('sms_logs')
            ->when($request->from_date, function ($q) use ($request) {
                $q->where('created_at', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($q) use ($request) {
                $q->where('created_at', '<=', $request->to_date);
            })
            ->when($request->e_wallet_no, function ($q) use ($request) {
                $q->where('e_wallet_no', $request->e_wallet_no);
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->search_any, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('customer_acc_no', 'like', '%' . $request->search_any . '%')
                        ->orWhere('txn', 'like', '%' . $request->search_any . '%')
                        ->orWhere('e_wallet_no', 'like', '%' . $request->search_any . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.sms_logs', compact('pageTitle', 'logs', 'distinctWalletNames'));
    }

    public function bank_account_log_summary(Request $request)
    {
        $search_date_column = 'created_at';
        $from_date = $request->filled('from_date') ? $request->from_date : now()->toDateString();
        $to_date = $request->filled('to_date') ? $request->to_date : now()->toDateString();

        // Banks = EWalletAccount
        $EwalletNames = EWalletAccount::distinct()->pluck('e_wallet_name')->toArray();

        // Bank accounts list filtered by selected EWalletAccount
        $bankAccountList = [];
        if ($request->filled('bank_name')) {
            $bankAccountList = EWalletAccount::where('e_wallet_name', $request->bank_name)
                ->pluck('name', 'gateway_code')->toArray();
        }

        $apisList = Api::pluck('name', 'id')->toArray();

        // Filter parameters
        $bank_name = $request->input('bank_name');
        $account_number = $request->input('account_number');
        $filter_status = $request->input('filter_status');
        $filter_type = $request->input('filter_type');
        $merchant = $request->input('merchants'); 

        // ----------------- Fetch Payments (Deposits) -----------------
        $payments = Payment::query()
            ->whereDate($search_date_column, '>=', $from_date)
            ->whereDate($search_date_column, '<=', $to_date);

        if ($bank_name) {
            $payments->where('e_wallet_name', $bank_name);
        }

        if ($account_number) {
            $payments->where('gateway_code', $account_number);
        }

        if ($filter_status) {
            $payments->where('status', $filter_status);
        }

        // ----------------- Fetch Payouts (Withdrawals) -----------------
        $payouts = Payout::query()
            ->whereDate($search_date_column, '>=', $from_date)
            ->whereDate($search_date_column, '<=', $to_date);

        if ($bank_name) {
            $payouts->where('e_wallet_name', $bank_name);
        }

        if ($account_number) {
            $payouts->where('gateway_code', $account_number);
        }

        if ($filter_status) {
            $payouts->where('status', $filter_status);
        }

        // ----------------- Fetch EWallet Transfers -----------------
        $ewalletTransfers = EWalletTransfer::query()
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date);

        if ($bank_name) {
            $ewalletTransfers->where('domain', $bank_name);
        }

        if ($account_number) {
            $ewalletTransfers->where('e_wallet_account_no', $account_number);
        }

        if ($filter_status) {
            $ewalletTransfers->where('status', $filter_status);
        }

        if ($filter_type) {
            $ewalletTransfers->where('transaction_type', $filter_type);
        }

        // ----------------- Get all data -----------------
        $deposits = $payments->get();
        $withdrawals = $payouts->get();
        $transfers = $ewalletTransfers->get();

        // ----------------- Merge & Process -----------------
        $merged = collect();

        $merged = $merged->merge($deposits->map(function ($item) {
            return (object)[
                'type' => 'deposit',
                'amount' => $item->amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'gateway_alias' => $item->gateway_alias
            ];
        }));

        $merged = $merged->merge($withdrawals->map(function ($item) {
            return (object)[
                'type' => 'withdrawal',
                'amount' => $item->amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'gateway_alias' => $item->gateway_alias
            ];
        }));

        $merged = $merged->merge($transfers->map(function ($item) {
            return (object)[
                'type' => $item->transaction_type == 3 ? 'transfer_in' : 'transfer_out',
                'amount' => $item->amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'gateway_alias' => $item->domain
            ];
        }));

        // ----------------- Summary Calculations -----------------
        $summary = [
            'total_transactions' => $merged->count(),
            'total_deposit_count' => $merged->where('type', 'deposit')->count(),
            'total_withdrawal_count' => $merged->where('type', 'withdrawal')->count(),
            'total_deposit_amount' => $merged->where('type', 'deposit')->sum('amount'),
            'total_withdrawal_amount' => $merged->where('type', 'withdrawal')->sum('amount'),
            'total_completed_deposit' => $merged->where('type', 'deposit')->where('status', 1)->count(),
            'total_rejected_deposit' => $merged->where('type', 'deposit')->where('status', 3)->count(),
            'total_completed_withdrawal' => $merged->where('type', 'withdrawal')->where('status', 1)->count(),
            'total_rejected_withdrawal' => $merged->where('type', 'withdrawal')->where('status', 3)->count(),
            'total_transfer_transactions' => $merged->filter(fn($t) => in_array($t->type, ['transfer_in', 'transfer_out']))->count(),
            'transfer_in' => $merged->where('type', 'transfer_in')->sum('amount'),
            'transfer_out' => $merged->where('type', 'transfer_out')->sum('amount'),
        ];

        // ----------------- Pagination -----------------
        $page = $request->get('page', 1);
        $perPage = 50;
        $transactions = new LengthAwarePaginator(
            $merged->sortByDesc('created_at')->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $pageTitle = "Bank Account Log Summary";
        return view('admin.reports.bank_account_log_summary', compact(
            'pageTitle',
            'from_date',
            'to_date',
            'EwalletNames',
            'transactions',
            'summary',
            'bank_name',
            'account_number',
            'filter_status',
            'filter_type',
            'bankAccountList',
            'apisList',
            'merchant',
        ));
    }


    public function daily_transection_summary(Request $request)
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $request->filled('date') ? $request->date : $now->toDateString();

        // Define e-wallets
        $wallets = ['Nagad', 'bKash', 'Rocket'];

        // Fetch all Payments
        $payments = Payment::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->where(function ($q) use ($wallets) {
                foreach ($wallets as $wallet) {
                    $q->orWhere('e_wallet_name', 'like', "%$wallet%");
                }
            })
            ->selectRaw("e_wallet_name, SUM(amount) as total_amount, COUNT(*) as record_count")
            ->groupBy('e_wallet_name')
            ->get()
            ->keyBy(function ($item) {
                return strtolower(Str::slug($item->e_wallet_name)) . '_d';
            });

        // Fetch all Payouts
        $payouts = Payout::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->where(function ($q) use ($wallets) {
                foreach ($wallets as $wallet) {
                    $q->orWhere('e_wallet_name', 'like', "%$wallet%");
                }
            })
            ->selectRaw("e_wallet_name, SUM(amount) as total_amount, COUNT(*) as record_count")
            ->groupBy('e_wallet_name')
            ->get()
            ->keyBy(function ($item) {
                return strtolower(Str::slug($item->e_wallet_name)) . '_w';
            });

        // E-Wallet Transfers - Incoming
        $in_transfers = EWalletTransaction::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->where(function ($q) use ($wallets) {
                foreach ($wallets as $wallet) {
                    $q->orWhere('to_e_wallet', 'like', "%$wallet%");
                }
            })
            ->selectRaw("to_e_wallet as e_wallet_name, SUM(amount) as total_amount, COUNT(*) as record_count")
            ->groupBy('to_e_wallet')
            ->get()
            ->keyBy(function ($item) {
                return strtolower(Str::slug($item->e_wallet_name)) . '_in';
            });

        // E-Wallet Transfers - Outgoing
        $out_transfers = EWalletTransaction::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->where(function ($q) use ($wallets) {
                foreach ($wallets as $wallet) {
                    $q->orWhere('from_e_wallet', 'like', "%$wallet%");
                }
            })
            ->selectRaw("from_e_wallet as e_wallet_name, SUM(amount) as total_amount, COUNT(*) as record_count")
            ->groupBy('from_e_wallet')
            ->get()
            ->keyBy(function ($item) {
                return strtolower(Str::slug($item->e_wallet_name)) . '_out';
            });

        // Merge all data
        $data = [];
        foreach ($wallets as $wallet) {
            $key = strtolower(Str::slug($wallet)); // e.g. 'nagad'
            $data["{$key}_d"] = $payments["{$key}_d"] ?? (object)['total_amount' => 0, 'record_count' => 0];
            $data["{$key}_w"] = $payouts["{$key}_w"] ?? (object)['total_amount' => 0, 'record_count' => 0];
            $data["{$key}_in"] = $in_transfers["{$key}_in"] ?? (object)['total_amount' => 0, 'record_count' => 0];
            $data["{$key}_out"] = $out_transfers["{$key}_out"] ?? (object)['total_amount' => 0, 'record_count' => 0];
        }

        $pageTitle = __('reports.daily_transection_summary');
        return view('admin.reports.daily_transection_summary', compact('pageTitle', 'data', 'date'));
    }




    public function merchant_charges_summary(Request $request)
{
    // Get paginated list of domains (as before)
    $domains = Api::where('type', 'Admin')
        ->where('website', '!=', env('APP_WEBSITE'))
        ->paginate(20);

    $domainIds = $domains->pluck('id');

    // Fetch deposits in one go
    $deposits = Payment::whereIn('api_id', $domainIds)
        ->where('status', 'Complete')
        ->groupBy('api_id')
        ->selectRaw('api_id, COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
        ->get()
        ->keyBy('api_id');

    // Fetch withdrawals in one go
    $withdrawals = Payout::whereIn('api_id', $domainIds)
        ->where('status', 'Complete')
        ->groupBy('api_id')
        ->selectRaw('api_id, COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
        ->get()
        ->keyBy('api_id');

    // Build summary data
    $data = [];
    foreach ($domains as $domain) {
        $deposit = $deposits[$domain->id] ?? (object)['deposit_amount' => 0, 'deposit_charges' => 0];
        $withdrawal = $withdrawals[$domain->id] ?? (object)['withdrawal_amount' => 0, 'withdrawal_charges' => 0];

        $data[] = [
            'partner' => $domain->name,
            'deposit_amount' => $deposit->deposit_amount,
            'deposit_charges' => $deposit->deposit_charges,
            'withdrawal_amount' => $withdrawal->withdrawal_amount,
            'withdrawal_charges' => $withdrawal->withdrawal_charges,
            'total_charges' => $deposit->deposit_charges + $withdrawal->withdrawal_charges,
        ];
    }

    $pageTitle = __('reports.merchant_charges_summary');
    return view('admin.reports.merchant_charges_summary', compact('pageTitle', 'domains', 'data'));
}



    public function merchant_charges_summary_search(Request $request)
    {
        $from_date = "";
        $to_date = "";
        $website = "";
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }
        if ($request->filled('website')) {
            $website = $request->website;
        }

        $domains = Api::where('type', 'Admin')->where('website', '!=', env('APP_WEBSITE'))->paginate(20);
        $partners = Api::where('type', 'Admin')->where('website', 'like', '%' . $website . '%')->where('website', '!=', env('APP_WEBSITE'))->get();

        foreach ($partners as $key => $domain) {
            $depositQuery = Payment::where('api_id', $domain->id)
                ->where('status', 'Complete');

            $withdrawalQuery = Payout::where('api_id', $domain->id)
                ->where('status', 'Complete');

            if (!empty($from_date)) {
                $depositQuery->whereDate('created_at', '>=', $from_date);
                $withdrawalQuery->whereDate('created_at', '>=', $from_date);
            }

            if (!empty($to_date)) {
                $depositQuery->whereDate('created_at', '<=', $to_date);
                $withdrawalQuery->whereDate('created_at', '<=', $to_date);
            }

            $deposit = $depositQuery->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')->first();
            $withdrawal = $withdrawalQuery->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')->first();
            $data[$key]['partner'] = $domain->name;
            $data[$key]['deposit_amount'] = $deposit->deposit_amount;
            $data[$key]['deposit_charges'] = $deposit->deposit_charges;
            $data[$key]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
            $data[$key]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
            $data[$key]['total_charges'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges;
        }

        $pageTitle = "Merchant Charges Summary";
        return view('admin.reports.merchant_charges_summary', compact('pageTitle', 'domains', 'data'));
    }


    public function partner_account_summary(Request $request)
    {
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');
        $website = $request->filled('website') ? $request->website : "";

        $domains = Api::where('type', 'Admin')->where('website', '!=', env('APP_WEBSITE'))->get();
        $partners = $domains->filter(function ($item) use ($website) {
            return str_contains($item->website, $website);
        });

        $partnerIds = $partners->pluck('id');

        // Pre-fetch deposits
        $deposits = Payment::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->whereIn('api_id', $partnerIds)
            ->where('status', 'Complete')
            ->selectRaw('DATE(created_at) as date, api_id, SUM(amount) as deposit_amount, SUM(charge) as deposit_charges')
            ->groupBy('date', 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '_' . $item->api_id);

        // Pre-fetch withdrawals
        $withdrawals = Payout::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->whereIn('api_id', $partnerIds)
            ->where('status', 'Complete')
            ->selectRaw('DATE(created_at) as date, api_id, SUM(amount) as withdrawal_amount, SUM(charge) as withdrawal_charges')
            ->groupBy('date', 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '_' . $item->api_id);

        // Pre-fetch fund status
        $fundStats = Payment::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->whereIn('api_id', $partnerIds)
            ->selectRaw('DATE(created_at) as date, api_id, COUNT(*) as total_records, SUM(CASE WHEN status = "Complete" THEN 1 ELSE 0 END) as status_1_count')
            ->groupBy('date', 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '_' . $item->api_id);

        $data = [];
        $count = 0;

        foreach ($partners as $partner) {
            $current = strtotime($from_date);
            $end = strtotime($to_date);

            while ($current <= $end) {
                $date = date('Y-m-d', $current);
                $key = $date . '_' . $partner->id;

                $deposit = $deposits[$key] ?? (object)['deposit_amount' => 0, 'deposit_charges' => 0];
                $withdrawal = $withdrawals[$key] ?? (object)['withdrawal_amount' => 0, 'withdrawal_charges' => 0];
                $fund = $fundStats[$key] ?? (object)['total_records' => 0, 'status_1_count' => 0];

                if ($deposit->deposit_amount > 0 || $withdrawal->withdrawal_amount > 0) {
                    $total_charges = $deposit->deposit_charges + $withdrawal->withdrawal_charges;
                    $daily_balance = $deposit->deposit_amount - $withdrawal->withdrawal_amount - $total_charges;
                    $success_rate = $fund->total_records > 0 ? $fund->status_1_count / $fund->total_records * 100 : 100;

                    $data[] = [
                        'partner' => $partner->name,
                        'date' => $date,
                        'deposit_amount' => $deposit->deposit_amount,
                        'deposit_charges' => $deposit->deposit_charges,
                        'withdrawal_amount' => $withdrawal->withdrawal_amount,
                        'withdrawal_charges' => $withdrawal->withdrawal_charges,
                        'total_charges' => $total_charges,
                        'daily_balance' => $daily_balance,
                        'success_rate' => (float) number_format($success_rate, 2, '.', ''),
                    ];
                }

                $current = strtotime('+1 day', $current);
            }
        }

        $pageTitle = __('reports.partner_account_summary');
        return view('admin.reports.partner_account_summary', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }




    public function partner_account_balance_summary(Request $request)
    {
        // $this->add_daily_partner_summary();
        // $this->add_daily_summary();
        // exit;
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');

        $domainsQuery = Api::where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            });

        if ($request->filled('website') && !empty($request->website)) {
            $domainsQuery->where('id', $request->website);
        }


        $partners = $domainsQuery->get();
        $partnerIds = $partners->pluck('id')->toArray();


        $dates = collect();
        for ($date = strtotime($from_date); $date <= strtotime($to_date); $date = strtotime('+1 day', $date)) {
            $dates->push(date('Y-m-d', $date));
        }

        // Preload data in bulk (grouped by date and api_id or partner_id)
        $deposits = Payment::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 'Complete')
            ->whereIn('api_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, api_id, SUM(amount) as deposit_amount, SUM(charge) as deposit_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $withdrawals = Payout::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 'Complete')
            ->whereIn('api_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, api_id, SUM(amount) as withdrawal_amount, SUM(charge) as withdrawal_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $settlements = Settlement::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 1)
            ->whereIn('partner_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, partner_id, SUM(amount) as settlement_amount, SUM(charges) as settlement_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'partner_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->partner_id);

        $adjustments = ApiTransaction::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->whereIn('partner_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, partner_id, SUM(amount) as adjustment_amount, SUM(charges) as adjustment_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'partner_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->partner_id);

        $commissions = PartnerCommission::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 1)
            ->whereIn('from_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, from_id, SUM(profit) as commission_amount")
            ->groupBy(DB::raw('DATE(created_at)'), 'from_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->from_id);

        $openingBalances = DailyPartnerSummary::whereIn('api_id', $partnerIds)
            ->whereBetween(DB::raw('DATE(created_at)'), [date('Y-m-d', strtotime($from_date . ' -1 day')), $to_date])
            ->select('api_id', 'closing_balance', DB::raw('DATE(created_at) as date'))
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $data = [];
        $count = 0;

        foreach ($dates as $date) {
            $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));

            foreach ($partners as $partner) {
                $key = $date . '-' . $partner->id;
                $prevKey = $prevDate . '-' . $partner->id;

                $deposit = $deposits[$key] ?? (object)['deposit_amount' => 0, 'deposit_charges' => 0];
                $withdrawal = $withdrawals[$key] ?? (object)['withdrawal_amount' => 0, 'withdrawal_charges' => 0];
                $settlement = $settlements[$key] ?? (object)['settlement_amount' => 0, 'settlement_charges' => 0];
                $adjustment = $adjustments[$key] ?? (object)['adjustment_amount' => 0, 'adjustment_charges' => 0];
                $commission = $commissions[$key] ?? (object)['commission_amount' => 0];

                $opening_balance = $openingBalances[$prevKey]->closing_balance ?? 0.00;
                $today_opening_balance = $openingBalances[$key]->closing_balance ?? 0.00;

                $total_charges = $deposit->deposit_charges + $withdrawal->withdrawal_charges + $settlement->settlement_charges + $adjustment->adjustment_charges;

                $closing_balance = $opening_balance + $adjustment->adjustment_amount - $adjustment->adjustment_charges + $commission->commission_amount + $deposit->deposit_amount - $deposit->deposit_charges - $withdrawal->withdrawal_amount - $withdrawal->withdrawal_charges - $settlement->settlement_amount - $settlement->settlement_charges;

                $differance = number_format($closing_balance - $today_opening_balance, 2);

                $data[] = [
                    'id' => $partner->id,
                    'partner' => $partner->name,
                    'date' => $date,
                    'opening_balance' => $opening_balance,
                    'deposit_amount' => $deposit->deposit_amount,
                    'deposit_charges' => $deposit->deposit_charges,
                    'withdrawal_amount' => $withdrawal->withdrawal_amount,
                    'withdrawal_charges' => $withdrawal->withdrawal_charges,
                    'settlement_amount' => $settlement->settlement_amount,
                    'settlement_charges' => $settlement->settlement_charges,
                    'adjustment' => $adjustment->adjustment_amount,
                    'adjustment_charges' => $adjustment->adjustment_charges,
                    'commission' => $commission->commission_amount,
                    'total_charges' => $total_charges,
                    'closing_balance' => $closing_balance,
                    'today_opening_balance' => $today_opening_balance,
                    'differance' => $differance,
                    'current_balance' => $partner->balance,
                ];
            }
        }

        $pageTitle = __('reports.partner_account_balance_summary_creations');
        $domains = Api::where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })->get();

        return view('admin.reports.partner_account_balance_summary', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }

    public function partner_account_balance_summaryv2(Request $request)
    {
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');

        $domainsQuery = Api::select('id' , 'name' , 'balance')->where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            });

        if ($request->filled('website') && !empty($request->website)) {
            $domainsQuery->where('id', $request->website);
        }

        $partners = $domainsQuery->get();
        $partnerIds = $partners->pluck('id')->toArray();


        $dates = collect();
        for ($date = strtotime($from_date); $date <= strtotime($to_date); $date = strtotime('+1 day', $date)) {
            $dates->push(date('Y-m-d', $date));
        }

        // Preload data in bulk (grouped by date and api_id or partner_id)
        $deposits = Payment::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 'Complete')
            ->whereIn('api_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, api_id, SUM(amount) as deposit_amount, SUM(charge) as deposit_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $withdrawals = Payout::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 'Complete')
            ->whereIn('api_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, api_id, SUM(amount) as withdrawal_amount, SUM(charge) as withdrawal_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $settlements = Settlement::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 1)
            ->whereIn('partner_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, partner_id, SUM(amount) as settlement_amount, SUM(charges) as settlement_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'partner_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->partner_id);

        $adjustments = ApiTransaction::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->whereIn('partner_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, partner_id, SUM(amount) as adjustment_amount, SUM(charges) as adjustment_charges")
            ->groupBy(DB::raw('DATE(created_at)'), 'partner_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->partner_id);

        $commissions = PartnerCommission::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->where('status', 1)
            ->whereIn('from_id', $partnerIds)
            ->selectRaw("DATE(created_at) as date, from_id, SUM(profit) as commission_amount")
            ->groupBy(DB::raw('DATE(created_at)'), 'from_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->from_id);

        $openingBalances = DailyPartnerSummary::whereIn('api_id', $partnerIds)
            ->whereBetween(DB::raw('DATE(created_at)'), [date('Y-m-d', strtotime($from_date . ' -1 day')), $to_date])
            ->select('api_id', 'closing_balance', DB::raw('DATE(created_at) as date'))
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $data = [];
        $count = 0;

        foreach ($dates as $date) {
            $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));

            foreach ($partners as $partner) {
                $key = $date . '-' . $partner->id;
                $prevKey = $prevDate . '-' . $partner->id;

                $deposit = $deposits[$key] ?? (object)['deposit_amount' => 0, 'deposit_charges' => 0];
                $withdrawal = $withdrawals[$key] ?? (object)['withdrawal_amount' => 0, 'withdrawal_charges' => 0];
                $settlement = $settlements[$key] ?? (object)['settlement_amount' => 0, 'settlement_charges' => 0];
                $adjustment = $adjustments[$key] ?? (object)['adjustment_amount' => 0, 'adjustment_charges' => 0];
                $commission = $commissions[$key] ?? (object)['commission_amount' => 0];

                $opening_balance = $openingBalances[$prevKey]->closing_balance ?? 0.00;
                $today_opening_balance = $openingBalances[$key]->closing_balance ?? 0.00;

                $total_charges = $deposit->deposit_charges + $withdrawal->withdrawal_charges + $settlement->settlement_charges + $adjustment->adjustment_charges;

                $closing_balance = $opening_balance + $adjustment->adjustment_amount - $adjustment->adjustment_charges + $commission->commission_amount + $deposit->deposit_amount - $deposit->deposit_charges - $withdrawal->withdrawal_amount - $withdrawal->withdrawal_charges - $settlement->settlement_amount - $settlement->settlement_charges;

                $differance = number_format($closing_balance - $today_opening_balance, 2);

                $data[] = [
                    'id' => $partner->id,
                    'partner' => $partner->name,
                    'date' => $date,
                    'opening_balance' => $opening_balance,
                    'deposit_amount' => $deposit->deposit_amount,
                    'deposit_charges' => $deposit->deposit_charges,
                    'withdrawal_amount' => $withdrawal->withdrawal_amount,
                    'withdrawal_charges' => $withdrawal->withdrawal_charges,
                    'settlement_amount' => $settlement->settlement_amount,
                    'settlement_charges' => $settlement->settlement_charges,
                    'adjustment' => $adjustment->adjustment_amount,
                    'adjustment_charges' => $adjustment->adjustment_charges,
                    'commission' => $commission->commission_amount,
                    'total_charges' => $total_charges,
                    'closing_balance' => $closing_balance,
                    'today_opening_balance' => $today_opening_balance,
                    'differance' => $differance,
                    'current_balance' => $partner->balance,
                ];
            }
        }

        $pageTitle = __('reports.partner_account_balance_summary_creations');
        $domains = Api::select('id' , 'name')->where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })->get();

        $pageTitle = "Dev Partner Account Balance Summary Creations";
        return view('admin.reports.partner_account_balance_summaryv2', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }

    public function add_daily_summary()
    {
        $timezone = config('app.timezone');
        $now = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-27 00:00:00', $timezone);
        $date = $now->toDateString();
        $EndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-27 23:59:00', $timezone);
        $oneDayBefore = $now->copy()->subDay()->toDateString();
        $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);


        // $timezone = config('app.timezone');
        // $now = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-28 00:00:00', $timezone);
        // $date = $now->toDateString();
        // $EndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-28 23:59:00', $timezone);
        // $oneDayBefore = $now->subDay()->toDateString();
        // $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);

        $EWalletAccounts = EWalletAccount::all()->keyBy('id');

        // Get all data in bulk
        $payments = Payment::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy(fn($item) => $item->e_wallet_name . '_' . $item->e_wallet_phone_number);

        $payouts = Payout::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy(fn($item) => $item->e_wallet_name . '_' . $item->e_wallet_phone_number);

        $transfers_in = EWalletTransaction::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy(fn($item) => $item->to_e_wallet . '_' . $item->to_account_no);

        $transfers_out = EWalletTransaction::where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy(fn($item) => $item->from_e_wallet . '_' . $item->from_account_no);

        // Fetch all previous day summaries in bulk
        $previousSummaries = DailyEWalletSummary::whereDate('created_at', $oneDayBefore)
            ->get()
            ->keyBy('e_wallet_id');

            foreach ($EWalletAccounts as $accountId => $account) {
                $key = $account->e_wallet_name . '_' . $account->account_no;

                $total_deposit = isset($payments[$key]) ? $payments[$key]->sum('amount') : 0.00;
                $total_withdrawal = isset($payouts[$key]) ? $payouts[$key]->sum('amount') : 0.00;
                $transfer_in = isset($transfers_in[$key]) ? $transfers_in[$key]->sum('amount') : 0.00;
                $transfer_out = isset($transfers_out[$key]) ? $transfers_out[$key]->sum('amount') : 0.00;

                $previousSummary = $previousSummaries[$accountId] ?? null;

                if (!$previousSummary) {
                    $closing_balance = 0;

                    DailyEWalletSummary::create([
                        'e_wallet_id' => $account->id,
                        'closing_balance' => $closing_balance,
                        'created_at' => $oneDayBeforeEndOfDay,
                        'updated_at' => $oneDayBeforeEndOfDay,
                    ]);
                } else {
                    $closing_balance = $previousSummary->closing_balance;
                }

                $new_closing_balance = $closing_balance + $total_deposit - $total_withdrawal + $transfer_in - $transfer_out;

                DailyEWalletSummary::create([
                    'e_wallet_id' => $account->id,
                    'closing_balance' => $new_closing_balance,
                    'actual_balance' => $account->balance,
                    'created_at' => $EndOfDay,
                    'updated_at' => $EndOfDay,
                ]);
            }

    }


    public function add_daily_partner_summary()
    {
        $timezone = config('app.timezone');
        $now = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-28 00:00:00', $timezone);
        $date = $now->toDateString();
        $EndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-28 23:59:00', $timezone);
        $oneDayBefore = $now->subDay()->toDateString();
        $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);



        $domains = Api::where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })->get();

        // Fetch all data outside the loop
        $createdData = $this->getDailyBalanceData($date, 'created_at');
        $completedData = $this->getDailyBalanceData($date, 'completed');



        foreach ($domains as $domain) {
            $id = $domain->id;

            $created = $createdData[$id] ?? ['not_found_balance' => 0, 'closing_balance' => 0];
            $completed = $completedData[$id] ?? ['not_found_balance' => 0, 'closing_balance' => 0];




            $record = DailyPartnerSummary::where('api_id', $id)
                ->whereDate('created_at', $oneDayBefore)
                ->first();

            if (!$record) {
                $prev = new DailyPartnerSummary();
                $prev->api_id = $id;



                $prev->closing_balance = $domain->balance - $created['not_found_balance'];
                $prev->completion_at_balance = $domain->balance - $completed['not_found_balance'];

                $created['closing_balance'] += $prev->closing_balance;
                $completed['closing_balance'] += $prev->completion_at_balance;

                $prev->created_at = $oneDayBeforeEndOfDay;
                $prev->updated_at = $oneDayBeforeEndOfDay;
                $prev->save();
            } else {
                $created['closing_balance'] += $record->closing_balance;
                $completed['closing_balance'] += $record->completion_at_balance;
            }

            $summary = new DailyPartnerSummary();
            $summary->api_id = $id;
            $summary->closing_balance = $created['closing_balance'];
            $summary->completion_at_balance = $completed['closing_balance'];
            $summary->actual_balance = $domain->balance;
            $summary->created_at = $EndOfDay;
            $summary->updated_at = $EndOfDay;
            $summary->save();
        }
    }


    public function getDailyBalanceData($date, $mode = 'created_at')
    {
        $fieldMap = [
            'created_at' => [
                'payment' => 'created_at',
                'payout' => 'created_at',
                'settlement' => 'created_at',
                'adjustment' => 'created_at',
                'commission' => 'created_at',
            ],
            'completed' => [
                'payment' => 'trans_complete_date',
                'payout' => 'completions_at',
                'settlement' => 'updated_at',
                'adjustment' => 'updated_at',
                'commission' => 'updated_at',
            ]
        ];

        $field = $fieldMap[$mode];

        $deposits = Payment::whereDate($field['payment'], $date)
            ->where('status', 'Complete')
            ->selectRaw('api_id, COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
            ->groupBy('api_id')->pluck('deposit_amount', 'api_id')->toArray();

        $depositCharges = Payment::whereDate($field['payment'], $date)
            ->where('status', 'Complete')
            ->selectRaw('api_id, COALESCE(SUM(charge), 0) as deposit_charges')
            ->groupBy('api_id')->pluck('deposit_charges', 'api_id')->toArray();

        $withdrawals = Payout::whereDate($field['payout'], $date)
            ->where('status', 'Complete')
            ->selectRaw('api_id, COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
            ->groupBy('api_id')->get()->keyBy('api_id');

        $settlements = Settlement::whereDate($field['settlement'], $date)
            ->where('status', 1)
            ->selectRaw('partner_id, COALESCE(SUM(amount), 0) as amount, COALESCE(SUM(charges), 0) as charges')
            ->groupBy('partner_id')->get()->keyBy('partner_id');

        $adjustments = ApiTransaction::whereDate($field['adjustment'], $date)
            ->selectRaw('partner_id, COALESCE(SUM(amount), 0) as amount, COALESCE(SUM(charges), 0) as charges')
            ->groupBy('partner_id')->get()->keyBy('partner_id');

        $commissions = PartnerCommission::whereDate($field['commission'], $date)
            ->where('status', 1)
            ->selectRaw('from_id, COALESCE(SUM(profit), 0) as commission')
            ->groupBy('from_id')->pluck('commission', 'from_id')->toArray();

        $results = [];

        foreach (array_keys($deposits + $depositCharges + $withdrawals->toArray() + $settlements->toArray() + $adjustments->toArray() + $commissions) as $id) {
            $deposit = $deposits[$id] ?? 0;
            $depositCharge = $depositCharges[$id] ?? 0;
            $withdraw = $withdrawals[$id]->withdrawal_amount ?? 0;
            $withdrawCharge = $withdrawals[$id]->withdrawal_charges ?? 0;
            $settle = $settlements[$id]->amount ?? 0;
            $settleCharge = $settlements[$id]->charges ?? 0;
            $adjust = $adjustments[$id]->amount ?? 0;
            $adjustCharge = $adjustments[$id]->charges ?? 0;
            $commission = $commissions[$id] ?? 0;

            $results[$id]['not_found_balance'] = $deposit - $adjust + $adjustCharge - $commission + $depositCharge + $withdraw + $withdrawCharge + $settle + $settleCharge;
            $results[$id]['closing_balance'] = $adjust - $adjustCharge + $commission + $deposit - $depositCharge - $withdraw - $withdrawCharge - $settle - $settleCharge;
        }

        return $results;
    }


    public function add_daily_partner_summary_old()
    {
        $timezone = config('app.timezone');
        //$now = Carbon::now($timezone);
        $now = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-28 00:00:00', $timezone);
        $date = $now->subDay()->toDateString();
        //dd($now);
        $EndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', '2025-05-28 23:59:00', $timezone);

        $oneDayBefore = $now->subDay()->toDateString();
        $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);

        // dd($oneDayBeforeEndOfDay);
        // exit;
        $domains = Api::where('type', 'Admin')
            ->where(function($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })
            ->get();
        foreach ($domains as $key => $domain) {
            $deposit = Payment::whereDate('created_at', $date)
                ->where('status', 'Complete')
                ->where('api_id', $domain->id)
                ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                ->first();

            $withdrawal = Payout::whereDate('created_at', $date)
                ->where('status', 'Complete')
                ->where('api_id', $domain->id)
                ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                ->first();

            $Settlement = Settlement::where('partner_id', $domain->id)->where('status', 1)->whereDate('created_at', $date)->selectRaw('COALESCE(SUM(amount), 0) as settlement_amount, COALESCE(SUM(charges), 0) as settlement_charges')->first();
            $adjustment = ApiTransaction::where('partner_id', $domain->id)->whereDate('created_at', $date)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
            $PartnerCommission = PartnerCommission::where('from_id', $domain->id)->where('status', 1)->whereDate('created_at', $date)->selectRaw('COALESCE(SUM(profit), 0) as commission_amount')->first();

            $data[$key]['deposit_amount'] = $deposit->deposit_amount;
            $data[$key]['deposit_charges'] = $deposit->deposit_charges;
            $data[$key]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
            $data[$key]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
            $data[$key]['settlement_amount'] = $Settlement->settlement_amount;
            $data[$key]['settlement_charges'] = $Settlement->settlement_charges;
            $data[$key]['adjustment'] = $adjustment->adjustment_amount;
            $data[$key]['adjustment_charges'] = $adjustment->adjustment_charges;
            $data[$key]['commission'] = $PartnerCommission->commission_amount;

            $record =  DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $oneDayBefore)->first();
            if (!$record) {
                $add_previous_record = new DailyPartnerSummary();
                $add_previous_record->api_id = $domain->id;
                $closing_balance = $domain->balance - $data[$key]['deposit_amount'] - $data[$key]['adjustment'] + $data[$key]['adjustment_charges'] - $data[$key]['commission'] + $data[$key]['deposit_charges'] + $data[$key]['withdrawal_amount'] + $data[$key]['withdrawal_charges'] + $data[$key]['settlement_amount'] + $data[$key]['settlement_charges'];
                $add_previous_record->closing_balance = $closing_balance;
                $add_previous_record->created_at = $oneDayBeforeEndOfDay;
                $add_previous_record->updated_at = $oneDayBeforeEndOfDay;
                $add_previous_record->save();
            } else {
                $closing_balance = $record->closing_balance;
            }

            $data[$key]['closing_balance'] = $closing_balance + $data[$key]['adjustment'] - $data[$key]['adjustment_charges'] + $data[$key]['commission'] + $data[$key]['deposit_amount'] - $data[$key]['deposit_charges'] - $data[$key]['withdrawal_amount'] - $data[$key]['withdrawal_charges'] - $data[$key]['settlement_amount'] - $data[$key]['settlement_charges'];

            $DailyPartnerSummary = new DailyPartnerSummary();
            $DailyPartnerSummary->api_id = $domain->id;
            $DailyPartnerSummary->closing_balance = $data[$key]['closing_balance'];
            $DailyPartnerSummary->actual_balance = $domain->balance;
            $DailyPartnerSummary->created_at = $EndOfDay;
            $DailyPartnerSummary->updated_at = $EndOfDay;
            $DailyPartnerSummary->save();
        }
    }




    public function partner_account_balance_summary_completions_old(Request $request)
    {

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $website = "";
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }


        $domains = Api::where('type', 'Admin')
            ->where(function($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })
        ->get();

        if ($request->filled('website') && !empty($request->website)) {
            $website = $request->website;
            $partners = Api::where('type', 'Admin')->where('id', $website)->get();
        }else{
            $partners = Api::where('type', 'Admin')
            ->where(function($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })
            ->get();
        }
        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);
            $carbonDate = Carbon::createFromFormat('Y-m-d', $currentDateFormatted);
            $oneDayBefore = $carbonDate->subDay();

            foreach ($partners as $key => $domain) {
                $deposit = Payment::whereDate('trans_complete_date', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->where('api_id', $domain->id)
                    ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                    ->first();

                $withdrawal = Payout::whereDate('completions_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->where('api_id', $domain->id)
                    ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                    ->first();

                $Settlement = Settlement::where('partner_id', $domain->id)->where('status', 1)->whereDate('updated_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as settlement_amount, COALESCE(SUM(charges), 0) as settlement_charges')->first();
                $adjustment = ApiTransaction::where('partner_id', $domain->id)->whereDate('updated_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
                $PartnerCommission = PartnerCommission::where('from_id', $domain->id)->where('status', 1)->whereDate('updated_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(profit), 0) as commission_amount')->first();
                // if ($deposit->deposit_amount > 0 || $withdrawal->withdrawal_amount > 0 || $Settlement->settlement_amount > 0 || $adjustment->adjustment_amount > 0 || $PartnerCommission->commission_amount > 0) {
                if (1==1) {
                    $data[$count]['partner'] = $domain->name;
                    $data[$count]['date'] = $currentDateFormatted;
                    $data[$count]['opening_balance'] = DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $oneDayBefore)->first()->completion_at_balance ?? 0.00;
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
                    $data[$count]['today_opening_balance'] = DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $currentDateFormatted)->first()->completion_at_balance ?? 0.00;
                    $data[$count]['differance'] = $data[$count]['closing_balance'] - $data[$count]['today_opening_balance'];
                    $data[$count]['differance'] = number_format($data[$count]['differance'], 2);
                    $data[$count]['current_balance'] = $domain->balance;
                    $count++;
                }
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "{{ __('reports.partner_account_balance_summary_completions') }}";
        return view('admin.reports.partner_account_balance_summary_completions', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }


    public function partner_account_balance_summary_completions(Request $request)
    {
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');

        $domainsQuery = Api::where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            });

        if ($request->filled('website') && !empty($request->website)) {
            $domainsQuery->where('id', $request->website);
        }

        $partners = $domainsQuery->get();
        $partnerIds = $partners->pluck('id')->toArray();

        $dates = collect();
        for ($date = strtotime($from_date); $date <= strtotime($to_date); $date = strtotime('+1 day', $date)) {
            $dates->push(date('Y-m-d', $date));
        }

        // Preload data in bulk (grouped by date and api_id or partner_id)
        $deposits = Payment::whereBetween(DB::raw('DATE(trans_complete_date)'), [$from_date, $to_date])
            ->where('status', 'Complete')
            ->whereIn('api_id', $partnerIds)
            ->selectRaw("DATE(trans_complete_date) as date, api_id, SUM(amount) as deposit_amount, SUM(charge) as deposit_charges")
            ->groupBy(DB::raw('DATE(trans_complete_date)'), 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $withdrawals = Payout::whereBetween(DB::raw('DATE(completions_at)'), [$from_date, $to_date])
            ->where('status', 'Complete')
            ->whereIn('api_id', $partnerIds)
            ->selectRaw("DATE(completions_at) as date, api_id, SUM(amount) as withdrawal_amount, SUM(charge) as withdrawal_charges")
            ->groupBy(DB::raw('DATE(completions_at)'), 'api_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $settlements = Settlement::whereBetween(DB::raw('DATE(updated_at)'), [$from_date, $to_date])
            ->where('status', 1)
            ->whereIn('partner_id', $partnerIds)
            ->selectRaw("DATE(updated_at) as date, partner_id, SUM(amount) as settlement_amount, SUM(charges) as settlement_charges")
            ->groupBy(DB::raw('DATE(updated_at)'), 'partner_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->partner_id);

        $adjustments = ApiTransaction::whereBetween(DB::raw('DATE(updated_at)'), [$from_date, $to_date])
            ->whereIn('partner_id', $partnerIds)
            ->selectRaw("DATE(updated_at) as date, partner_id, SUM(amount) as adjustment_amount, SUM(charges) as adjustment_charges")
            ->groupBy(DB::raw('DATE(updated_at)'), 'partner_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->partner_id);

        $commissions = PartnerCommission::whereBetween(DB::raw('DATE(updated_at)'), [$from_date, $to_date])
            ->where('status', 1)
            ->whereIn('from_id', $partnerIds)
            ->selectRaw("DATE(updated_at) as date, from_id, SUM(profit) as commission_amount")
            ->groupBy(DB::raw('DATE(updated_at)'), 'from_id')
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->from_id);

        $openingBalances = DailyPartnerSummary::whereIn('api_id', $partnerIds)
            ->whereBetween(DB::raw('DATE(created_at)'), [date('Y-m-d', strtotime($from_date . ' -1 day')), $to_date])
            ->select('api_id', 'completion_at_balance', DB::raw('DATE(created_at) as date'))
            ->get()
            ->keyBy(fn($item) => $item->date . '-' . $item->api_id);

        $data = [];
        $count = 0;

        foreach ($dates as $date) {
            $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));

            foreach ($partners as $partner) {
                $key = $date . '-' . $partner->id;
                $prevKey = $prevDate . '-' . $partner->id;

                $deposit = $deposits[$key] ?? (object)['deposit_amount' => 0, 'deposit_charges' => 0];
                $withdrawal = $withdrawals[$key] ?? (object)['withdrawal_amount' => 0, 'withdrawal_charges' => 0];
                $settlement = $settlements[$key] ?? (object)['settlement_amount' => 0, 'settlement_charges' => 0];
                $adjustment = $adjustments[$key] ?? (object)['adjustment_amount' => 0, 'adjustment_charges' => 0];
                $commission = $commissions[$key] ?? (object)['commission_amount' => 0];

                $opening_balance = $openingBalances[$prevKey]->completion_at_balance ?? 0.00;
                $today_opening_balance = $openingBalances[$key]->completion_at_balance ?? 0.00;

                $total_charges = $deposit->deposit_charges + $withdrawal->withdrawal_charges + $settlement->settlement_charges + $adjustment->adjustment_charges;

                $closing_balance = $opening_balance + $adjustment->adjustment_amount - $adjustment->adjustment_charges + $commission->commission_amount + $deposit->deposit_amount - $deposit->deposit_charges - $withdrawal->withdrawal_amount - $withdrawal->withdrawal_charges - $settlement->settlement_amount - $settlement->settlement_charges;

                $differance = number_format($closing_balance - $today_opening_balance, 2);

                $data[] = [
                    'id' => $partner->id,
                    'partner' => $partner->name,
                    'date' => $date,
                    'opening_balance' => $opening_balance,
                    'deposit_amount' => $deposit->deposit_amount,
                    'deposit_charges' => $deposit->deposit_charges,
                    'withdrawal_amount' => $withdrawal->withdrawal_amount,
                    'withdrawal_charges' => $withdrawal->withdrawal_charges,
                    'settlement_amount' => $settlement->settlement_amount,
                    'settlement_charges' => $settlement->settlement_charges,
                    'adjustment' => $adjustment->adjustment_amount,
                    'adjustment_charges' => $adjustment->adjustment_charges,
                    'commission' => $commission->commission_amount,
                    'total_charges' => $total_charges,
                    'closing_balance' => $closing_balance,
                    'today_opening_balance' => $today_opening_balance,
                    'differance' => $differance,
                    'current_balance' => $partner->balance,
                ];
            }
        }

        $pageTitle = "Partner Account Balance Summary Completions";
        $domains = Api::where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })->get();

        return view('admin.reports.partner_account_balance_summary_completions', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }

    public function revenue_center(Request $request)
{
    $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-01');
    $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');

    // Fetch deposit charges grouped by date
    $depositData = Payment::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->where('status', 'Complete')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->selectRaw('DATE(created_at) as date, COALESCE(SUM(charge), 0) as deposit_charges')
        ->get()
        ->keyBy('date');

    // Fetch withdrawal charges grouped by date
    $withdrawalData = Payout::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->where('status', 'Complete')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->selectRaw('DATE(created_at) as date, COALESCE(SUM(charge), 0) as withdrawal_charges')
        ->get()
        ->keyBy('date');

    // Fetch commission profit grouped by date
    $commissionData = PartnerCommission::whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->where('status', 1)
        ->groupBy(DB::raw('DATE(created_at)'))
        ->selectRaw('DATE(created_at) as date, COALESCE(SUM(profit), 0) as commission_profit')
        ->get()
        ->keyBy('date');

    // Collect all unique dates
    $allDates = collect()
        ->merge($depositData->keys())
        ->merge($withdrawalData->keys())
        ->merge($commissionData->keys())
        ->unique()
        ->sort()
        ->values();

    $data = [];
    foreach ($allDates as $date) {
        $deposit = $depositData->get($date)?->deposit_charges ?? 0;
        $withdrawal = $withdrawalData->get($date)?->withdrawal_charges ?? 0;
        $commission = $commissionData->get($date)?->commission_profit ?? 0;

        if ($deposit > 0 || $withdrawal > 0 || $commission > 0) {
            $data[] = [
                'date' => $date,
                'deposit_charges' => $deposit,
                'withdrawal_charges' => $withdrawal,
                'commission_profit' => $commission,
                'daily_profit' => $deposit + $withdrawal - $commission,
            ];
        }
    }

    $pageTitle = "{{ __('reports.revenue_center') }}";
    return view('admin.reports.revenue_center', compact('pageTitle', 'data', 'from_date', 'to_date'));
}





public function logs(Request $request)
{
    $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
    $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');
    $sort_by = $request->get('sort_by', 'created_at');
    $orderval = $request->get('order', 'desc');
    $website = $request->get('website', '');

    $domains = Api::where('type', 'Admin')
        ->where(function($query) {
            $query->where('website', '!=', env('APP_WEBSITE'))
                ->orWhereNull('website');
        })
        ->get();

    $data = [];
    if (!empty($website)) {
        $data = Log::where('partner_id', $website)
            ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->with('api')
            ->orderBy("logs.{$sort_by}", $orderval)
            ->get();
    }

    // Collect IDs by type
    $idsByType = [
        1 => [], // Payments
        2 => [], // Payouts
        3 => [], // ApiTransactions
        4 => [], // Settlements
        5 => [], // PartnerCommissions
        7 => [], // Payouts again
    ];

    foreach ($data as $item) {
        if (isset($idsByType[$item->transection_type])) {
            $idsByType[$item->transection_type][] = $item->transection_id;
        }
    }

    // Bulk fetch related models
    $payments = Payment::whereIn('id', $idsByType[1])->get()->keyBy('id');
    $payouts = Payout::whereIn('id', array_merge($idsByType[2], $idsByType[7]))->get()->keyBy('id');
    $apiTransactions = ApiTransaction::whereIn('id', $idsByType[3])->get()->keyBy('id');
    $settlements = Settlement::whereIn('id', $idsByType[4])->get()->keyBy('id');
    $partnerCommissions = PartnerCommission::whereIn('id', $idsByType[5])->get()->keyBy('id');

    // Preload APIs for PartnerCommission
    $apiIds = $partnerCommissions->pluck('api_id')->unique()->filter();
    $apiMap = Api::whereIn('id', $apiIds)->get()->keyBy('id');

    $filter_data = [];
    foreach ($data as $key => $item) {
        $filter_data[$key] = [
            'id' => $item->id,
            'partner' => $item->api->name ?? '',
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
        ];

        switch ($item->transection_type) {
            case 1:
                $trans = $payments[$item->transection_id] ?? null;
                if ($trans) {
                    $filter_data[$key] = array_merge($filter_data[$key], [
                        'amount' => $trans->amount,
                        'charge' => $trans->charge,
                        'sender' => $trans->sender,
                        'e_wallet_name' => $trans->e_wallet_name,
                        'e_wallet_phone_number' => $trans->e_wallet_phone_number,
                        'e_wallet_type' => $trans->e_wallet_type,
                        'partner_transection_id' => $trans->partner_transection_id,
                        'txn_id' => $trans->txn_id,
                        'txn_created_at' => $trans->created_at,
                    ]);
                }
                break;

            case 2:
            case 7:
                $trans = $payouts[$item->transection_id] ?? null;
                if ($trans) {
                    $filter_data[$key] = array_merge($filter_data[$key], [
                        'amount' => $trans->amount,
                        'charge' => $trans->charge,
                        'sender' => $trans->user_account_no,
                        'e_wallet_name' => $trans->e_wallet_name,
                        'e_wallet_phone_number' => $trans->e_wallet_phone_number,
                        'e_wallet_type' => $trans->e_wallet_type,
                        'partner_transection_id' => $trans->partner_transection_id,
                        'txn_id' => $trans->txn_id,
                        'txn_created_at' => $trans->created_at,
                    ]);
                }
                break;

            case 3:
                $trans = $apiTransactions[$item->transection_id] ?? null;
                if ($trans) {
                    $filter_data[$key] = array_merge($filter_data[$key], [
                        'amount' => $trans->amount,
                        'charge' => $trans->charges,
                        'e_wallet_name' => $trans->source,
                        'txn_id' => $trans->txn,
                        'txn_created_at' => $trans->created_at,
                    ]);
                }
                break;

            case 4:
                $trans = $settlements[$item->transection_id] ?? null;
                if ($trans) {
                    $filter_data[$key] = array_merge($filter_data[$key], [
                        'amount' => $trans->amount,
                        'charge' => $trans->charges,
                        'sender' => $trans->account_no,
                        'e_wallet_name' => $trans->source_name,
                        'e_wallet_type' => $trans->source,
                        'txn_created_at' => $trans->created_at,
                    ]);
                }
                break;

            case 5:
                $trans = $partnerCommissions[$item->transection_id] ?? null;
                if ($trans) {
                    $filter_data[$key]['amount'] = $trans->amount;
                    $filter_data[$key]['charge'] = $trans->charges;
                    $filter_data[$key]['e_wallet_type'] = $trans->type == 1 ? 'Deposit' : 'Withdrawal';
                    $filter_data[$key]['txn_created_at'] = $trans->created_at;
                    $filter_data[$key]['sender'] = $apiMap[$trans->api_id]->name ?? '';
                }
                break;
        }
    }

    $pageTitle = __('reports.partner_balance_logs');
    return view('admin.reports.logs', compact('pageTitle', 'domains', 'filter_data', 'from_date', 'to_date', 'orderval'));
}



    public function cal(Request $request)
    {
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');
        $api_id = $request->filled('website') && !empty($request->website) ? $request->website : 8;

        // Fetch once instead of in view or loop
        $domains = Api::where('type', 'Admin')
            ->where(function ($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })
            ->get();

        // Prepare mapping of API ids to names to avoid DB query in loop
        $apiNames = Api::pluck('name', 'id');

        $data = collect();

        // Common date filter
        $dateFilter = fn ($query) => $query->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date]);

        // Get Deposits
        $deposits = Payment::where('status', 'Complete')
            ->where('api_id', $api_id)
            ->where($dateFilter)
            ->get()
            ->map(function ($deposit) {
                return [
                    'date_time' => $deposit->created_at->timestamp,
                    'final_amount' => $deposit->amount - $deposit->charge,
                    'balance' => 0,
                    'transection_type' => 1,
                    'transection_id' => $deposit->id,
                    'partner_id' => $deposit->api_id,
                    'amount' => $deposit->amount,
                    'charge' => $deposit->charge,
                    'sender' => $deposit->sender,
                    'e_wallet_name' => $deposit->e_wallet_name,
                    'e_wallet_phone_number' => $deposit->e_wallet_phone_number,
                    'e_wallet_type' => $deposit->e_wallet_type,
                    'partner_transection_id' => $deposit->partner_transection_id,
                    'txn_id' => $deposit->txn_id,
                    'txn_created_at' => $deposit->created_at,
                ];
            });

        // Get Withdrawals
        $withdrawals = Payout::where('status', 'Complete')
            ->where('api_id', $api_id)
            ->where($dateFilter)
            ->get()
            ->map(function ($withdrawal) {
                return [
                    'date_time' => $withdrawal->created_at->timestamp,
                    'final_amount' => -($withdrawal->amount + $withdrawal->charge),
                    'balance' => 0,
                    'transection_type' => 2,
                    'transection_id' => $withdrawal->id,
                    'partner_id' => $withdrawal->api_id,
                    'amount' => $withdrawal->amount,
                    'charge' => $withdrawal->charge,
                    'sender' => $withdrawal->user_account_no,
                    'e_wallet_name' => $withdrawal->e_wallet_name,
                    'e_wallet_phone_number' => $withdrawal->e_wallet_phone_number,
                    'e_wallet_type' => $withdrawal->e_wallet_type,
                    'partner_transection_id' => $withdrawal->partner_transection_id,
                    'txn_id' => $withdrawal->txn_id,
                    'txn_created_at' => $withdrawal->created_at,
                ];
            });

        // Get ApiTransactions
        $apiTransactions = ApiTransaction::where('partner_id', $api_id)
            ->where($dateFilter)
            ->get()
            ->map(function ($txn) {
                return [
                    'date_time' => $txn->created_at->timestamp,
                    'final_amount' => $txn->amount - $txn->charges,
                    'balance' => 0,
                    'transection_type' => 3,
                    'transection_id' => $txn->id,
                    'partner_id' => $txn->partner_id,
                    'amount' => $txn->amount,
                    'charge' => '',
                    'sender' => '',
                    'e_wallet_name' => $txn->source,
                    'e_wallet_phone_number' => '',
                    'e_wallet_type' => '',
                    'partner_transection_id' => '',
                    'txn_id' => $txn->txn,
                    'txn_created_at' => $txn->created_at,
                ];
            });

        // Get Settlements
        $settlements = Settlement::where('status', 1)
            ->where('partner_id', $api_id)
            ->where($dateFilter)
            ->get()
            ->map(function ($settlement) {
                return [
                    'date_time' => $settlement->created_at->timestamp,
                    'final_amount' => -($settlement->amount + $settlement->charge),
                    'balance' => 0,
                    'transection_type' => 4,
                    'transection_id' => $settlement->id,
                    'partner_id' => $settlement->partner_id,
                    'amount' => $settlement->amount,
                    'charge' => $settlement->charges,
                    'sender' => $settlement->account_no,
                    'e_wallet_name' => $settlement->source_name,
                    'e_wallet_phone_number' => '',
                    'e_wallet_type' => $settlement->source,
                    'partner_transection_id' => '',
                    'txn_id' => '',
                    'txn_created_at' => $settlement->created_at,
                ];
            });

        // Get PartnerCommissions
        $partnerCommissions = PartnerCommission::where('status', 1)
            ->where('from_id', $api_id)
            ->where($dateFilter)
            ->get()
            ->map(function ($commission) use ($apiNames) {
                return [
                    'date_time' => $commission->created_at->timestamp,
                    'final_amount' => $commission->profit,
                    'balance' => 0,
                    'transection_type' => 5,
                    'transection_id' => $commission->id,
                    'partner_id' => $commission->from_id,
                    'amount' => $commission->amount,
                    'charge' => $commission->charges,
                    'sender' => $apiNames[$commission->api_id] ?? '',
                    'e_wallet_type' => $commission->type == 1 ? 'Deposit' : ($commission->type == 2 ? 'Withdrawal' : ''),
                    'e_wallet_name' => '',
                    'e_wallet_phone_number' => '',
                    'partner_transection_id' => '',
                    'txn_id' => '',
                    'txn_created_at' => $commission->created_at,
                ];
            });

        // Merge all data and sort
        $filter_data = $data
            ->merge($deposits)
            ->merge($withdrawals)
            ->merge($apiTransactions)
            ->merge($settlements)
            ->merge($partnerCommissions)
            ->sortBy('date_time')
            ->values()
            ->toArray();

        $pageTitle = "{{ __('reports.transections_logs') }}";

        return view('admin.reports.logs2', compact('pageTitle', 'filter_data', 'from_date', 'to_date', 'domains'));
    }



    public function cal2(Request $request)
    {
        // $from_date = date('Y-m-01');
        // $from_date = '2023-09-01';
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $website = "";
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }

        $domains = Api::where('type', 'Admin')
            ->where(function($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })
            ->get();

            // dd($request->all());

            if ($request->filled('website') && !empty($request->website)) {
                $api_id = $request->website;
            }else{
                $api_id = 8;
            }


        $data = [];
        $count = 0;

        $deposits = Payment::where('status', 'Complete')
        ->select([DB::raw('SUM(amount) AS payment_amount'), DB::raw('SUM(charge) AS payment_charge')])
        ->where('api_id', $api_id)
        ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->first();

        // print_r($deposits);

        $withdrawals = Payout::where('status', 'Complete')
        ->select([DB::raw('SUM(amount) AS payment_amount'), DB::raw('SUM(charge) AS payment_charge')])
        ->where('api_id', $api_id)
            ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
            ->first();



        $ApiTransactions = ApiTransaction::select([DB::raw('SUM(amount) AS payment_amount'), DB::raw('SUM(charges) AS payment_charge')])->where('partner_id', $api_id)
        ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->first();


        $Settlements = Settlement::select([DB::raw('SUM(amount) AS payment_amount'), DB::raw('SUM(charges) AS payment_charge')])
        ->where('status', 1)
        ->where('partner_id', $api_id)
        ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->first();



        $PartnerCommissions = PartnerCommission::select([DB::raw('SUM(profit) AS partner_profit')])
        ->where('status', 1)
        ->where('from_id', $api_id)
        ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
        ->first();


        // dd($deposits);
        // $filter_data = $data;

        $pageTitle = __('reports.transections_logs');
        return view('admin.reports.logs3', compact('deposits' , 'withdrawals' , 'ApiTransactions' , 'PartnerCommissions' , 'Settlements','pageTitle', 'from_date', 'to_date','domains'));
    }

    public function master_report(Request $request)
    {
        $from_date = $request->filled('from_date') ? $request->from_date : date('Y-m-d');
        $to_date = $request->filled('to_date') ? $request->to_date : date('Y-m-d');

        $domains = Api::where('type', 'Admin')->where('website', '!=', env('APP_WEBSITE'))->get();
        $partners = Api::where('type', 'Admin')->get();

        $dates = collect();
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        while ($currentDate <= $endDate) {
            $dates->push(date('Y-m-d', $currentDate));
            $currentDate = strtotime('+1 day', $currentDate);
        }

        // Fetch and group all needed data
        $deposits = Payment::whereBetween('created_at', [$from_date, $to_date])
            ->where('status', 'Complete')
            ->selectRaw("DATE(created_at) as date, SUM(amount) as deposit_amount, SUM(charge) as deposit_charges, SUM(e_wallet_charges) as deposit_e_wallet_charges, SUM(commission) as deposit_commission, COUNT(*) as deposit_record_count")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $withdrawals = Payout::whereBetween('created_at', [$from_date, $to_date])
            ->where('status', 'Complete')
            ->selectRaw("DATE(created_at) as date, SUM(amount) as withdrawal_amount, SUM(charge) as withdrawal_charges, SUM(e_wallet_charges) as withdrawal_e_wallet_charges, SUM(commission) as withdrawal_commission, COUNT(*) as withdrawal_record_count")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $settlements = Settlement::where('status', 1)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw("DATE(created_at) as date, SUM(amount) as settlement_amount, SUM(charges) as settlement_charges")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $top_ups = ApiTransaction::where('adjustment', 4)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw("DATE(created_at) as date, SUM(amount) as adjustment_amount, SUM(charges) as adjustment_charges")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $adjustments = ApiTransaction::where('adjustment', '!=', 4)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw("DATE(created_at) as date, SUM(amount) as adjustment_amount, SUM(charges) as adjustment_charges")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $partnerCommissions = PartnerCommission::where('status', 1)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw("DATE(created_at) as date, SUM(profit) as commission_amount")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $transfers = EWalletTransfer::whereBetween('transaction_date_time', [$from_date, $to_date])
            ->selectRaw("DATE(transaction_date_time) as date, SUM(charges) as transfer_charges")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dailySummaries = DailyEWalletSummary::whereBetween('created_at', [$from_date, $to_date])
            ->selectRaw("DATE(created_at) as date, SUM(actual_balance) as e_wallet_balance")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $e_wallet_balance_today = EWalletAccount::selectRaw('COALESCE(SUM(balance), 0) as e_wallet_balance')->value('e_wallet_balance');

        $data = [];
        foreach ($dates as $date) {
            $deposit = $deposits->get($date) ?? (object)[
                'deposit_record_count' => 0,
                'deposit_amount' => 0,
                'deposit_charges' => 0,
                'deposit_e_wallet_charges' => 0,
                'deposit_commission' => 0
            ];

            $withdrawal = $withdrawals->get($date) ?? (object)[
                'withdrawal_record_count' => 0,
                'withdrawal_amount' => 0,
                'withdrawal_charges' => 0,
                'withdrawal_e_wallet_charges' => 0,
                'withdrawal_commission' => 0
            ];

            $settlement = $settlements->get($date) ?? (object)[
                'settlement_amount' => 0,
                'settlement_charges' => 0
            ];

            $top_up = $top_ups->get($date) ?? (object)[
                'adjustment_amount' => 0,
                'adjustment_charges' => 0
            ];

            $adjustment = $adjustments->get($date) ?? (object)[
                'adjustment_amount' => 0,
                'adjustment_charges' => 0
            ];

            $partnerCommission = $partnerCommissions->get($date) ?? (object)[
                'commission_amount' => 0
            ];

            $transfer = $transfers->get($date) ?? (object)[
                'transfer_charges' => 0
            ];

            $total_charges = $deposit->deposit_charges + $withdrawal->withdrawal_charges;

            $revenue = ($deposit->deposit_charges + $withdrawal->withdrawal_charges - $partnerCommission->commission_amount +
                    $adjustment->adjustment_charges + $top_up->adjustment_charges + $settlement->settlement_charges) +
                    ($deposit->deposit_commission - $deposit->deposit_e_wallet_charges +
                    $withdrawal->withdrawal_commission - $withdrawal->withdrawal_e_wallet_charges - $transfer->transfer_charges);

            $total = ($date == date('Y-m-d')) ? $e_wallet_balance_today : ($dailySummaries->get($date)->e_wallet_balance ?? 0);

            $data[] = [
                'date' => $date,
                'deposit_record_count' => $deposit->deposit_record_count,
                'deposit_amount' => $deposit->deposit_amount,
                'deposit_charges' => $deposit->deposit_charges,
                'deposit_e_wallet_charges' => $deposit->deposit_e_wallet_charges,
                'deposit_commission' => $deposit->deposit_commission,
                'withdrawal_record_count' => $withdrawal->withdrawal_record_count,
                'withdrawal_amount' => $withdrawal->withdrawal_amount,
                'withdrawal_charges' => $withdrawal->withdrawal_charges,
                'withdrawal_e_wallet_charges' => $withdrawal->withdrawal_e_wallet_charges,
                'withdrawal_commission' => $withdrawal->withdrawal_commission,
                'total_charges' => $total_charges,
                'settlement_amount' => $settlement->settlement_amount,
                'settlement_charges' => $settlement->settlement_charges,
                'top_up_amount' => $top_up->adjustment_amount,
                'top_up_charges' => $top_up->adjustment_charges,
                'adjustment_amount' => $adjustment->adjustment_amount,
                'adjustment_charges' => $adjustment->adjustment_charges,
                'transfer_charges' => $transfer->transfer_charges,
                'commission_amount' => $partnerCommission->commission_amount,
                'revenue' => $revenue,
                'total' => $total,
            ];
        }


        $total_deposit_qty = array_sum(array_column($data, 'deposit_record_count'));
        $total_deposit_amount = array_sum(array_column($data, 'deposit_amount'));
        $total_deposit_charges = array_sum(array_column($data, 'deposit_charges'));
        $total_deposit_e_wallet_charges = array_sum(array_column($data, 'deposit_e_wallet_charges'));
        $total_deposit_commission = array_sum(array_column($data, 'deposit_commission'));
        $total_withdrawal_qty = array_sum(array_column($data, 'withdrawal_record_count'));
        $total_withdrawal_amount = array_sum(array_column($data, 'withdrawal_amount'));
        $total_withdrawal_charges = array_sum(array_column($data, 'withdrawal_charges'));
        $total_commission_amount = array_sum(array_column($data, 'commission_amount'));
        $total_top_up_amount = array_sum(array_column($data, 'top_up_amount'));
        $total_top_up_charges = array_sum(array_column($data, 'top_up_charges'));
        $total_adjustment_amount = array_sum(array_column($data, 'adjustment_amount'));
        $total_adjustment_charges = array_sum(array_column($data, 'adjustment_charges'));
        $total_settlement_amount = array_sum(array_column($data, 'settlement_amount'));
        $total_revenue = array_sum(array_column($data, 'revenue'));

        $pageTitle = __('reports.partner_account_summary');
        return view('admin.reports.master_report', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date', 'total_deposit_qty', 'total_deposit_amount', 'total_deposit_charges', 'total_deposit_e_wallet_charges', 'total_deposit_commission', 'total_withdrawal_qty', 'total_withdrawal_amount', 'total_withdrawal_charges', 'total_commission_amount', 'total_top_up_amount', 'total_top_up_charges', 'total_adjustment_amount', 'total_adjustment_charges', 'total_settlement_amount', 'total_revenue'));
    }
    public function commissionBreakdown(Request $request)
    {
        $date = $request->input('date');
        $type = $request->input('type'); // 'deposit' or 'withdrawal'

        if (!in_array($type, ['deposit', 'withdrawal'])) {
            return response()->json(['html' => '<p>Invalid type provided.</p>']);
        }

        $model = $type === 'deposit' ? Payment::class : Payout::class;

        $commissions = $model::whereDate('created_at', $date)
            ->where('status', 'Complete')
            ->selectRaw('e_wallet_name, SUM(commission) as total_commission')
            ->groupBy('e_wallet_name')
            ->get();

        if ($commissions->isEmpty()) {
            return response()->json(['html' => '<p>No commission found for this date.</p>']);
        }

        $html = '';
        foreach ($commissions as $item) {
            $html .= '<div><strong>' . strtoupper(e($item->e_wallet_name)) . ':</strong> ' . number_format($item->total_commission, 2) . '</div>';
        }

        return response()->json(['html' => $html]);
    }


    public function fix_partner_summary_closing_balance(Request $request)
    {
        $amount = (float) $request->input('amount');       // may be negative or positive
        $apiId = (int) $request->input('id');
        $recordDate = $request->input('record_date');      // format Y-m-d

        if ($apiId === 0 || !$recordDate) {
            return redirect()->back()->with('error', 'Missing required parameters.');
        }

        DB::table('daily_partner_summary')
            ->where('api_id', $apiId)
            ->whereDate('created_at', '>=', $recordDate)
            ->update([
                'closing_balance' => DB::raw("closing_balance + ({$amount})")
            ]);

        return redirect()->back()->with('success', 'Closing balance updated successfully.');
    }

}
