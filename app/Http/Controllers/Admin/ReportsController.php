<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\Settlement;
use Illuminate\Http\Request;
use App\Models\ApiTransaction;
use App\Models\EWalletAccount;
use App\Models\EWalletTransfer;
use App\Models\PartnerCommission;
use App\Models\EWalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\DailyPartnerSummary;
use App\Models\DailyEWalletSummary;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function live_ewallet_balance()
    {
        $data = EWalletAccount::orderBy('e_wallet_name', 'asc')->get();
        $sumBalance = $data->sum('balance');
        $sumDailySent = $data->sum('daily_sent');
        $sumDailyReceived = $data->sum('daily_received');
        // dd($sumDailyReceived);
        $pageTitle = "Live E-Wallet Balance";
        return view('admin.reports.live_ewallet_balance', compact('pageTitle', 'data', 'sumBalance', 'sumDailySent', 'sumDailyReceived'));
    }

    public function daily_ewallet_summary(Request $request)
    {
        // $this->add_daily_summary();
        // exit;

        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->toDateString();
        $oneDayBefore = $now->subDay()->toDateString();
        if ($request->filled('date')) {
            $date = $request->date;
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $oneDayBefore = $carbonDate->subDay();
        }
        $EWalletAccounts = EWalletAccount::paginate(20);
        foreach ($EWalletAccounts as $key => $account) {
            $data[$key]['e_wallet_name'] = $account->e_wallet_name;
            $data[$key]['account_no'] = $account->account_no;
            $data[$key]['opening_balance'] = DailyEWalletSummary::where('e_wallet_id', $account->id)->whereDate('created_at', $oneDayBefore)->first()->closing_balance ?? 0.00;
            $data[$key]['total_deposit'] = Payment::where('e_wallet_name', $account->e_wallet_name)->where('e_wallet_phone_number', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['total_withdrawal'] = Payout::where('e_wallet_name', $account->e_wallet_name)->where('e_wallet_phone_number', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['transfer_in'] = EWalletTransaction::where('to_e_wallet', $account->e_wallet_name)->where('to_account_no', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['transfer_out'] = EWalletTransaction::where('from_e_wallet', $account->e_wallet_name)->where('from_account_no', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['closing_balance'] = $data[$key]['opening_balance'] + $data[$key]['total_deposit'] - $data[$key]['total_withdrawal'] + $data[$key]['transfer_in'] - $data[$key]['transfer_out'];
        }
        $pageTitle = "Daily E-Wallet Summary";
        return view('admin.reports.daily_ewallet_summary', compact('pageTitle', 'date', 'data','EWalletAccounts'));
    }



    public function daily_transection_summary(Request $request)
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->toDateString();
        if ($request->filled('date')) {
            $date = $request->date;
        }

        $data['nagad_d'] = Payment::where('e_wallet_name', 'like', '%Nagad%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['bkash_d'] = Payment::where('e_wallet_name', 'like', '%bKash%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['rocket_d'] = Payment::where('e_wallet_name', 'like', '%Rocket%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['nagad_w'] = Payout::where('e_wallet_name', 'like', '%Nagad%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['bkash_w'] = Payout::where('e_wallet_name', 'like', '%bKash%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['rocket_w'] = Payout::where('e_wallet_name', 'like', '%Rocket%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['nagad_in'] = EWalletTransaction::where('to_e_wallet', 'like', '%Nagad%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['bkash_in'] = EWalletTransaction::where('to_e_wallet', 'like', '%bKash%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['rocket_in'] = EWalletTransaction::where('to_e_wallet', 'like', '%Rocket%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['nagad_out'] = EWalletTransaction::where('from_e_wallet', 'like', '%Nagad%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['bkash_out'] = EWalletTransaction::where('from_e_wallet', 'like', '%bKash%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $data['rocket_out'] = EWalletTransaction::where('from_e_wallet', 'like', '%Rocket%')
            ->where('status', 'Complete')
            ->whereDate('created_at', $date)
            ->selectRaw('SUM(amount) as total_amount, COUNT(*) as record_count')
            ->first();

        $pageTitle = "Daily Transection Summary";
        return view('admin.reports.daily_transection_summary', compact('pageTitle', 'data', 'date'));
    }



    public function merchant_charges_summary(Request $request)
    {

        $domains = Api::where('type', 'Admin')->where('website', '!=', env('APP_WEBSITE'))->paginate(20);
        foreach ($domains as $key => $domain) {
            $deposit = Payment::where('api_id', $domain->id)
                ->where('status', 'Complete')
                ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                ->first();

            $withdrawal = Payout::where('api_id', $domain->id)
                ->where('status', 'Complete')
                ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                ->first();
            $data[$key]['partner'] = $domain->name;
            $data[$key]['deposit_amount'] = $deposit->deposit_amount;
            $data[$key]['deposit_charges'] = $deposit->deposit_charges;
            $data[$key]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
            $data[$key]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
            $data[$key]['total_charges'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges;
        }
        $pageTitle = "Merchant Charges Summary";
        return view('admin.reports.merchant_charges_summary', compact('pageTitle', 'domains', 'data','domains'));
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
        // $from_date = date('Y-m-01');
        $from_date = date('Y-m-d');
        // $from_date = '2023-09-01';
        $to_date = date('Y-m-d');
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


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            foreach ($partners as $key => $domain) {
                $deposit = Payment::whereDate('created_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->where('api_id', $domain->id)
                    ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                    ->first();

                $withdrawal = Payout::whereDate('created_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->where('api_id', $domain->id)
                    ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                    ->first();

                    $fund = Payment::whereDate('created_at', $currentDateFormatted)
                        ->where('api_id', $domain->id)
                        ->selectRaw('COUNT(*) as total_records')
                        ->selectRaw('COUNT(CASE WHEN status = 1 THEN 1 END) as status_1_count')
                        ->first();

                if ($deposit->deposit_amount > 0 || $withdrawal->withdrawal_amount > 0) {
                    $data[$count]['partner'] = $domain->name;
                    $data[$count]['date'] = $currentDateFormatted;
                    $data[$count]['deposit_amount'] = $deposit->deposit_amount;
                    $data[$count]['deposit_charges'] = $deposit->deposit_charges;
                    $data[$count]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
                    $data[$count]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
                    $data[$count]['total_charges'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges;
                    $data[$count]['daily_balance'] = $deposit->deposit_amount - $withdrawal->withdrawal_amount - $deposit-
                    deposit_charges - $withdrawal->withdrawal_charges;
                    $data[$count]['success_rate'] =  $fund->total_records>0?$fund->status_1_count / $fund->total_records * 100 : 100;
                    $count++;
                }
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Partner Account Summary";
        return view('admin.reports.partner_account_summary', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }



    public function partner_account_balance_summary(Request $request)
    {
        // $this->add_daily_partner_summary();
        // exit;

        // $from_date = date('Y-m-01');
        $from_date = date('Y-m-d');
        // $from_date = '2023-09-01';
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
                $deposit = Payment::whereDate('created_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->where('api_id', $domain->id)
                    ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                    ->first();

                    // dd($deposit);

                $withdrawal = Payout::whereDate('created_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->where('api_id', $domain->id)
                    ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                    ->first();

                $Settlement = Settlement::where('partner_id', $domain->id)->where('status', 1)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as settlement_amount, COALESCE(SUM(charges), 0) as settlement_charges')->first();
                $adjustment = ApiTransaction::where('partner_id', $domain->id)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
                $PartnerCommission = PartnerCommission::where('from_id', $domain->id)->where('status', 1)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(profit), 0) as commission_amount')->first();
                // if ($deposit->deposit_amount > 0 || $withdrawal->withdrawal_amount > 0 || $Settlement->settlement_amount > 0 || $adjustment->adjustment_amount > 0 || $PartnerCommission->commission_amount > 0) {
                if (1==1) {
                    $data[$count]['id'] = $domain->id;
                    $data[$count]['partner'] = $domain->name;
                    $data[$count]['date'] = $currentDateFormatted;
                    $data[$count]['opening_balance'] = DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $oneDayBefore)->first()->closing_balance ?? 0.00;
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
                    $data[$count]['today_opening_balance'] = DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $currentDateFormatted)->first()->closing_balance ?? 0.00;
                    $data[$count]['differance'] = $data[$count]['closing_balance'] - $data[$count]['today_opening_balance'];
                    $data[$count]['differance'] = number_format($data[$count]['differance'], 2);
                    $data[$count]['current_balance'] = $domain->balance;
                    $count++;
                }
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Partner Account Balance Summary Creations";
        return view('admin.reports.partner_account_balance_summary', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }



    public function partner_account_balance_summary_completions(Request $request)
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
                    $data[$count]['opening_balance'] = DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $oneDayBefore)->first()->created_at_balance ?? 0.00;
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
                    $data[$count]['today_opening_balance'] = DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $currentDateFormatted)->first()->created_at_balance ?? 0.00;
                    $data[$count]['differance'] = $data[$count]['closing_balance'] - $data[$count]['today_opening_balance'];
                    $data[$count]['differance'] = number_format($data[$count]['differance'], 2);
                    $data[$count]['current_balance'] = $domain->balance;
                    $count++;
                }
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Partner Account Balance Summary Completions";
        return view('admin.reports.partner_account_balance_summary_completions', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }

    public function revenue_center(Request $request)
    {
        $from_date = date('Y-m-01');
        // $from_date = '2023-09-01';
        $to_date = date('Y-m-d');
        $website = "";
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }


        $domains = Api::where('type', 'Admin')->where('website', '!=', env('APP_WEBSITE'))->get();


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

            $deposit = Payment::whereDate('created_at', $currentDateFormatted)
                ->where('status', 'Complete')
                ->selectRaw('COALESCE(SUM(charge), 0) as deposit_charges')
                ->first();

            $withdrawal = Payout::whereDate('created_at', $currentDateFormatted)
                ->where('status', 'Complete')
                ->selectRaw('COALESCE(SUM(charge), 0) as withdrawal_charges')
                ->first();

            $PartnerCommission = PartnerCommission::whereDate('created_at', $currentDateFormatted)
                ->where('status', 1)
                ->selectRaw('COALESCE(SUM(profit), 0) as commission_profit')
                ->first();
            if ($deposit->deposit_charges > 0 || $withdrawal->withdrawal_charges > 0 || $PartnerCommission->commission_profit > 0) {
                $data[$count]['date'] = $currentDateFormatted;
                $data[$count]['deposit_charges'] = $deposit->deposit_charges;
                $data[$count]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
                $data[$count]['commission_profit'] = $PartnerCommission->commission_profit;
                $data[$count]['daily_profit'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges - $PartnerCommission->commission_profit;
                $count++;
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Revenue Center";
        return view('admin.reports.revenue_center', compact('pageTitle', 'data', 'from_date', 'to_date'));
    }




    public function logs(Request $request)
    {

        $from_date = date('Y-m-d');
        // $from_date = '2023-09-01';
        $sort_by = $request->get('sort_by', 'created_at');
        $orderval = $request->get('order', 'desc');

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
                $website = $request->website;
                // $data = Log::orderBy($sort_by, $orderval)->where('partner_id', $website)->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])->with('api')->get();
                $data = Log::where('partner_id', $website)->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])->with('api')->orderBy('logs.created_at', 'desc')->get();
            }else{
                $data = [];
            }

            $filter_data = [];
            foreach ($data as $key => $item) {
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

            // dd($filter_data);
        $pageTitle = "Partner Balance Logs";
        return view('admin.reports.logs', compact('pageTitle', 'domains', 'filter_data', 'from_date', 'to_date' , 'orderval'));
    }


    public function cal(Request $request)
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
                            ->where('api_id', $api_id)
                            ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
                            ->get();

                    foreach ($deposits as $deposit) {
                        $data[$count]['date_time'] = $deposit->created_at->timestamp;
                        $data[$count]['final_amount'] = $deposit->amount - $deposit->charge;
                        $data[$count]['balance'] = 0;
                        $data[$count]['transection_type'] = 1;
                        $data[$count]['transection_id'] = $deposit->id;
                        $data[$count]['partner_id'] = $deposit->api_id;
                        $data[$count]['amount'] =  $deposit->amount;
                        $data[$count]['charge'] =  $deposit->charge;
                        $data[$count]['sender'] =  $deposit->sender;
                        $data[$count]['e_wallet_name'] =  $deposit->e_wallet_name;
                        $data[$count]['e_wallet_phone_number'] =  $deposit->e_wallet_phone_number;
                        $data[$count]['e_wallet_type'] =  $deposit->e_wallet_type;
                        $data[$count]['partner_transection_id'] =  $deposit->partner_transection_id;
                        $data[$count]['txn_id'] =  $deposit->txn_id;
                        $data[$count]['txn_created_at'] =  $deposit->created_at;
                        $count++;

                    }


                $withdrawals = Payout::where('status', 'Complete')
                    ->where('api_id', $api_id)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
                    ->get();

                    foreach ($withdrawals as $withdrawal) {
                        $data[$count]['date_time'] = $withdrawal->created_at->timestamp;
                        $data[$count]['final_amount'] = -($withdrawal->amount + $withdrawal->charge);
                        $data[$count]['balance'] = 0;
                        $data[$count]['transection_type'] = 2;
                        $data[$count]['transection_id'] = $withdrawal->id;
                        $data[$count]['partner_id'] = $withdrawal->api_id;
                        $data[$count]['amount'] =  $withdrawal->amount;
                        $data[$count]['charge'] =  $withdrawal->charge;
                        $data[$count]['sender'] =  $withdrawal->user_account_no;
                        $data[$count]['e_wallet_name'] =  $withdrawal->e_wallet_name;
                        $data[$count]['e_wallet_phone_number'] =  $withdrawal->e_wallet_phone_number;
                        $data[$count]['e_wallet_type'] =  $withdrawal->e_wallet_type;
                        $data[$count]['partner_transection_id'] =  $withdrawal->partner_transection_id;
                        $data[$count]['txn_id'] =  $withdrawal->txn_id;
                        $data[$count]['txn_created_at'] =  $withdrawal->created_at;
                        $count++;
                    }

                    $ApiTransactions = ApiTransaction::where('partner_id', $api_id)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
                    ->get();

                    foreach ($ApiTransactions as $ApiTransaction) {
                        $data[$count]['date_time'] = $ApiTransaction->created_at->timestamp;
                        $data[$count]['final_amount'] = $ApiTransaction->amount - $ApiTransaction->charges;
                        $data[$count]['balance'] = 0;
                        $data[$count]['transection_type'] = 3;
                        $data[$count]['transection_id'] = $ApiTransaction->id;
                        $data[$count]['partner_id'] = $ApiTransaction->partner_id;
                        $data[$count]['amount'] =  $ApiTransaction->amount;
                        $data[$count]['e_wallet_name'] =  $ApiTransaction->source;
                        $data[$count]['txn_id'] =  $ApiTransaction->txn;
                        $data[$count]['txn_created_at'] =  $ApiTransaction->created_at;
                        $data[$count]['charge'] =  "";
                        $data[$count]['sender'] =  "";
                        $data[$count]['e_wallet_phone_number'] =  "";
                        $data[$count]['e_wallet_type'] =  "";
                        $data[$count]['partner_transection_id'] =  "";
                        $count++;
                    }


                    $Settlements = Settlement::where('status', 1)
                    ->where('partner_id', $api_id)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
                    ->get();

                    foreach ($Settlements as $Settlement) {
                        $data[$count]['date_time'] = $Settlement->created_at->timestamp;
                        $data[$count]['final_amount'] = -($Settlement->amount + $Settlement->charge);
                        $data[$count]['balance'] = 0;
                        $data[$count]['transection_type'] = 4;
                        $data[$count]['transection_id'] = $Settlement->id;
                        $data[$count]['partner_id'] = $Settlement->partner_id;
                        $data[$count]['amount'] =  $Settlement->amount;
                        $data[$count]['charge'] =  $Settlement->charges;
                        $data[$count]['sender'] =  $Settlement->account_no;
                        $data[$count]['e_wallet_name'] =  $Settlement->source_name;
                        $data[$count]['e_wallet_type'] =  $Settlement->source;
                        $data[$count]['txn_created_at'] =  $Settlement->created_at;
                        $data[$count]['e_wallet_phone_number'] =  "";
                        $data[$count]['partner_transection_id'] =  "";
                        $data[$count]['txn_id'] =  "";
                        $count++;
                    }

                    $PartnerCommissions = PartnerCommission::where('status', 1)
                    ->where('from_id', $api_id)
                    ->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date])
                    ->get();

                    foreach ($PartnerCommissions as $PartnerCommission) {
                        $data[$count]['date_time'] = $PartnerCommission->created_at->timestamp;
                        $data[$count]['final_amount'] = $PartnerCommission->profit;
                        $data[$count]['balance'] = 0;
                        $data[$count]['transection_type'] = 5;
                        $data[$count]['transection_id'] = $PartnerCommission->id;
                        $data[$count]['partner_id'] = $PartnerCommission->from_id;
                        $data[$count]['sender'] =  "";
                        $sender = Api::where('id', $PartnerCommission->api_id)->first();
                        if($sender){
                            $data[$count]['sender'] =  $sender->name;
                        }

                        $data[$count]['amount'] =  $PartnerCommission->amount;
                        $data[$count]['charge'] =  $PartnerCommission->charges;

                        $data[$count]['e_wallet_type'] =  "";
                        if($PartnerCommission->type==1){
                            $data[$count]['e_wallet_type'] =  "Deposit";
                        }
                        if($PartnerCommission->type==2){
                            $data[$count]['e_wallet_type'] =  "Withdrawal";
                        }
                        $data[$count]['txn_created_at'] =  $PartnerCommission->created_at;
                        $data[$count]['e_wallet_name'] =  "";
                        $data[$count]['e_wallet_phone_number'] =  "";
                        $data[$count]['partner_transection_id'] =  "";
                        $data[$count]['txn_id'] =  "";

                        $count++;
                    }

                    usort($data, function ($a, $b) {
                            return $a['date_time'] - $b['date_time'];
                        });

                    // $filteredData = array_filter($data, function($item) use ($partner) {
                        //     return $item['partner_id'] == $partner->id;
                        // });
                        // $filteredData = array_values($filteredData);
        $filter_data = $data;
        $pageTitle = "Transections Logs";
        return view('admin.reports.logs2', compact('pageTitle', 'filter_data', 'from_date', 'to_date','domains'));
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

        $pageTitle = "Transections Logs";
        return view('admin.reports.logs3', compact('deposits' , 'withdrawals' , 'ApiTransactions' , 'PartnerCommissions' , 'Settlements','pageTitle', 'from_date', 'to_date','domains'));
    }

    public function master_report(Request $request){
        $from_date = date('Y-m-d');
        // $from_date = '2024-06-01';
        $to_date = date('Y-m-d');
        $website = "";
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }

        $domains = Api::where('type', 'Admin')->where('website', '!=', env('APP_WEBSITE'))->get();
        $partners = Api::where('type', 'Admin')->get();


        $data = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        $count = 0;
        while ($currentDate <= $endDate) {
            $currentDateFormatted = date('Y-m-d', $currentDate);

                $deposit = Payment::whereDate('created_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges, COALESCE(SUM(e_wallet_charges), 0) as deposit_e_wallet_charges, COALESCE(SUM(commission), 0) as deposit_commission, COUNT(*) as deposit_record_count')
                    ->first();

                $withdrawal = Payout::whereDate('created_at', $currentDateFormatted)
                    ->where('status', 'Complete')
                    ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges,  COALESCE(SUM(e_wallet_charges), 0) as withdrawal_e_wallet_charges, COALESCE(SUM(commission), 0) as withdrawal_commission, COUNT(*) as withdrawal_record_count')
                    ->first();

                    $Settlement = Settlement::where('status', 1)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as settlement_amount, COALESCE(SUM(charges), 0) as settlement_charges')->first();
                    $top_up = ApiTransaction::where('adjustment', 4)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
                    $adjustment = ApiTransaction::where('adjustment','!=', 4)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
                    $PartnerCommission = PartnerCommission::where('status', 1)->whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(profit), 0) as commission_amount')->first();

                    $EWalletTransfer = EWalletTransfer::whereDate('transaction_date_time', $currentDateFormatted)->selectRaw('COALESCE(SUM(charges), 0) as transfer_charges')->first();

                    $EWalletAccount = EWalletAccount::selectRaw('COALESCE(SUM(balance), 0) as e_wallet_balance')->first();

                if (1==1) {
                    $data[$count]['date'] = $currentDateFormatted;
                    $data[$count]['deposit_record_count'] = $deposit->deposit_record_count;
                    $data[$count]['deposit_amount'] = $deposit->deposit_amount;
                    $data[$count]['deposit_charges'] = $deposit->deposit_charges;
                    $data[$count]['deposit_e_wallet_charges'] = $deposit->deposit_e_wallet_charges;
                    $data[$count]['deposit_commission'] = $deposit->deposit_commission;
                    $data[$count]['withdrawal_record_count'] = $withdrawal->withdrawal_record_count;
                    $data[$count]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
                    $data[$count]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
                    $data[$count]['withdrawal_e_wallet_charges'] = $withdrawal->withdrawal_e_wallet_charges;
                    $data[$count]['withdrawal_commission'] = $withdrawal->withdrawal_commission;
                    $data[$count]['total_charges'] = $deposit->deposit_charges + $withdrawal->withdrawal_charges;
                    $data[$count]['settlement_amount'] = $Settlement->settlement_amount;
                    $data[$count]['settlement_charges'] = $Settlement->settlement_charges;
                    $data[$count]['top_up_amount'] = $top_up->adjustment_amount;
                    $data[$count]['top_up_charges'] = $top_up->adjustment_charges;
                    $data[$count]['adjustment_amount'] = $adjustment->adjustment_amount;
                    $data[$count]['adjustment_charges'] = $adjustment->adjustment_charges;
                     $data[$count]['transfer_charges'] = $EWalletTransfer->transfer_charges;
                    $data[$count]['commission_amount'] = $PartnerCommission->commission_amount;

                     $data[$count]['revenue'] = ($deposit->deposit_charges+$withdrawal->withdrawal_charges-$PartnerCommission->commission_amount+$adjustment->adjustment_charges+$top_up->adjustment_charges+$Settlement->settlement_charges) + ($deposit->deposit_commission-$deposit->deposit_e_wallet_charges+$withdrawal->withdrawal_commission-$withdrawal->withdrawal_e_wallet_charges-$EWalletTransfer->transfer_charges);

                    if($currentDateFormatted==date('Y-m-d')){
                        $data[$count]['total'] = $EWalletAccount->e_wallet_balance;
                    }else{
                        $closing_balance = DailyEWalletSummary::whereDate('created_at', $currentDateFormatted)->selectRaw('COALESCE(SUM(actual_balance), 0) as e_wallet_balance')->first();
                        $data[$count]['total'] = $closing_balance->e_wallet_balance;
                    }

                    $count++;
                }


            $currentDate = strtotime('+1 day', $currentDate);
        }

        $pageTitle = "Partner Account Summary";
        return view('admin.reports.master_report', compact('pageTitle', 'domains', 'data', 'from_date', 'to_date'));
    }
}
