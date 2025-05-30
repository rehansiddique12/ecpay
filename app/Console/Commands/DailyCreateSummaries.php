<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\Settlement;
use App\Models\ApiTransaction;
use App\Models\EWalletAccount;
use Illuminate\Console\Command;
use App\Models\PartnerCommission;
use App\Models\EWalletTransaction;
use App\Models\DailyEWalletSummary;
use App\Models\DailyPartnerSummary;
use Illuminate\Support\Facades\Log;

class DailyCreateSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return Command::SUCCESS;

        $this->add_daily_summary();
        $this->add_daily_partner_summary();
        $data = ['foo' => 'bar', 'baz' => 'qux'];
        Log::info('Logging some data', $data);
        $this->info('Data logged successfully!');
    }



    public function add_daily_summary()
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->toDateString();
        $oneDayBefore = $now->subDay()->toDateString();
        $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);
        $EWalletAccounts = EWalletAccount::get();
        foreach ($EWalletAccounts as $key => $account) {
            $data[$key]['total_deposit'] = Payment::where('e_wallet_name', $account->e_wallet_name)->where('e_wallet_phone_number', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['total_withdrawal'] = Payout::where('e_wallet_name', $account->e_wallet_name)->where('e_wallet_phone_number', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['transfer_in'] = EWalletTransaction::where('to_e_wallet', $account->e_wallet_name)->where('to_account_no', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $data[$key]['transfer_out'] = EWalletTransaction::where('from_e_wallet', $account->e_wallet_name)->where('from_account_no', $account->account_no)->where('status', 'Complete')->whereDate('created_at', $date)->sum('amount') ?? 0.00;
            $record =  DailyEWalletSummary::where('e_wallet_id', $account->id)->whereDate('created_at', $oneDayBefore)->first();
            if (!$record) {
                $add_previous_record = new DailyEWalletSummary();
                $add_previous_record->e_wallet_id = $account->id;
                $closing_balance = $account->balance - $data[$key]['total_deposit'] + $data[$key]['total_withdrawal'] - $data[$key]['transfer_in'] + $data[$key]['transfer_out'];
                $add_previous_record->closing_balance = $closing_balance;
                $add_previous_record->created_at = $oneDayBeforeEndOfDay;
                $add_previous_record->updated_at = $oneDayBeforeEndOfDay;
                $add_previous_record->save();
            } else {
                $closing_balance = $record->closing_balance;
            }
            $data[$key]['closing_balance'] = $closing_balance + $data[$key]['total_deposit'] - $data[$key]['total_withdrawal'] + $data[$key]['transfer_in'] - $data[$key]['transfer_out'];
            $DailyEWalletSummary = new DailyEWalletSummary();
            $DailyEWalletSummary->e_wallet_id = $account->id;
            $DailyEWalletSummary->closing_balance = $data[$key]['closing_balance'];
            $DailyEWalletSummary->actual_balance = $account->balance;
            $DailyEWalletSummary->save();
        }
    }


    public function add_daily_partner_summary()
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->subDay()->toDateString();
        $EndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 23:59:00', $timezone);
        $oneDayBefore = $now->subDay()->toDateString();
        $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);
        $domains = Api::where('type', 'Admin')
            ->where(function($query) {
                $query->where('website', '!=', env('APP_WEBSITE'))
                    ->orWhereNull('website');
            })
            ->get();
        foreach ($domains as $key => $domain) {

            $closing_balance_created_at = $this->create_at_balance_calculation($domain,$date,$oneDayBeforeEndOfDay,$oneDayBefore,$key);
            $closing_balance_completion_at = $this->completions_at_balance_calculation($domain,$date,$oneDayBeforeEndOfDay,$oneDayBefore,$key);
            
            $record =  DailyPartnerSummary::where('api_id', $domain->id)->whereDate('created_at', $oneDayBefore)->first();
            if (!$record) {
                $add_previous_record = new DailyPartnerSummary();
                $add_previous_record->api_id = $domain->id;
                $closing_balance = $domain->balance - $closing_balance_created_at['not_found_balance'];
                $completion_closing_balance = $domain->balance - $closing_balance_completion_at['not_found_balance'];

                $add_previous_record->closing_balance = $closing_balance;
                $add_previous_record->completion_at_balance = $completion_closing_balance;

                $add_previous_record->created_at = $oneDayBeforeEndOfDay;
                $add_previous_record->updated_at = $oneDayBeforeEndOfDay;
                $add_previous_record->save();
            } else {
                $closing_balance = $record->closing_balance;
                $completion_closing_balance = $record->completion_at_balance;
            }
            
            $DailyPartnerSummary = new DailyPartnerSummary();
            $DailyPartnerSummary->api_id = $domain->id;
            $DailyPartnerSummary->closing_balance = $closing_balance + $closing_balance_created_at['closing_balance'];
            $DailyPartnerSummary->completion_at_balance = $completion_closing_balance + $closing_balance_completion_at['closing_balance'];
            $DailyPartnerSummary->actual_balance = $domain->balance;
            $DailyPartnerSummary->created_at = $EndOfDay;
            $DailyPartnerSummary->updated_at = $EndOfDay;
            $DailyPartnerSummary->save();
        }
    }



    public function create_at_balance_calculation($domain,$date,$oneDayBeforeEndOfDay,$oneDayBefore,$key)
    {
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

            $newData['not_found_balance'] = $data[$key]['deposit_amount'] - $data[$key]['adjustment'] + $data[$key]['adjustment_charges'] - $data[$key]['commission'] + $data[$key]['deposit_charges'] + $data[$key]['withdrawal_amount'] + $data[$key]['withdrawal_charges'] + $data[$key]['settlement_amount'] + $data[$key]['settlement_charges'];  
            $newData['closing_balance'] = $data[$key]['adjustment'] - $data[$key]['adjustment_charges'] + $data[$key]['commission'] + $data[$key]['deposit_amount'] - $data[$key]['deposit_charges'] - $data[$key]['withdrawal_amount'] - $data[$key]['withdrawal_charges'] - $data[$key]['settlement_amount'] - $data[$key]['settlement_charges'];
            return $newData;
    }

    public function completions_at_balance_calculation($domain,$date,$oneDayBeforeEndOfDay,$oneDayBefore,$key)
    {

        $deposit = Payment::whereDate('trans_complete_date', $date)
                ->where('status', 'Complete')
                ->where('api_id', $domain->id)
                ->selectRaw('COALESCE(SUM(amount), 0) as deposit_amount, COALESCE(SUM(charge), 0) as deposit_charges')
                ->first();

            $withdrawal = Payout::whereDate('completions_at', $date)
                ->where('status', 'Complete')
                ->where('api_id', $domain->id)
                ->selectRaw('COALESCE(SUM(amount), 0) as withdrawal_amount, COALESCE(SUM(charge), 0) as withdrawal_charges')
                ->first();

            $Settlement = Settlement::where('partner_id', $domain->id)->where('status', 1)->whereDate('updated_at', $date)->selectRaw('COALESCE(SUM(amount), 0) as settlement_amount, COALESCE(SUM(charges), 0) as settlement_charges')->first();
            $adjustment = ApiTransaction::where('partner_id', $domain->id)->whereDate('updated_at', $date)->selectRaw('COALESCE(SUM(amount), 0) as adjustment_amount, COALESCE(SUM(charges), 0) as adjustment_charges')->first();
            $PartnerCommission = PartnerCommission::where('from_id', $domain->id)->where('status', 1)->whereDate('updated_at', $date)->selectRaw('COALESCE(SUM(profit), 0) as commission_amount')->first();
            
            $data[$key]['deposit_amount'] = $deposit->deposit_amount;
            $data[$key]['deposit_charges'] = $deposit->deposit_charges;
            $data[$key]['withdrawal_amount'] = $withdrawal->withdrawal_amount;
            $data[$key]['withdrawal_charges'] = $withdrawal->withdrawal_charges;
            $data[$key]['settlement_amount'] = $Settlement->settlement_amount;
            $data[$key]['settlement_charges'] = $Settlement->settlement_charges;
            $data[$key]['adjustment'] = $adjustment->adjustment_amount;
            $data[$key]['adjustment_charges'] = $adjustment->adjustment_charges;
            $data[$key]['commission'] = $PartnerCommission->commission_amount;

            $newData['not_found_balance'] = $data[$key]['deposit_amount'] - $data[$key]['adjustment'] + $data[$key]['adjustment_charges'] - $data[$key]['commission'] + $data[$key]['deposit_charges'] + $data[$key]['withdrawal_amount'] + $data[$key]['withdrawal_charges'] + $data[$key]['settlement_amount'] + $data[$key]['settlement_charges'];  
            $newData['closing_balance'] = $data[$key]['adjustment'] - $data[$key]['adjustment_charges'] + $data[$key]['commission'] + $data[$key]['deposit_amount'] - $data[$key]['deposit_charges'] - $data[$key]['withdrawal_amount'] - $data[$key]['withdrawal_charges'] - $data[$key]['settlement_amount'] - $data[$key]['settlement_charges'];
            return $newData;

    }
}
