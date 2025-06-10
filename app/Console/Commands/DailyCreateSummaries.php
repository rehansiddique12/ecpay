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
    protected $signature = 'app:daily-creat-summaries';

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


        Log::info('Successfully Run DailyCreateSummaries Cron');

        
        $this->add_daily_partner_summary();

        $this->add_daily_summary();
    }



    public function add_daily_summary()
    {
        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);
        $date = $now->toDateString();
        $oneDayBefore = $now->copy()->subDay()->toDateString();
        $oneDayBeforeEndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $oneDayBefore . ' 23:59:00', $timezone);

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
                    $closing_balance = $account->balance - $total_deposit + $total_withdrawal - $transfer_in + $transfer_out;
            
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
                ]);
            }
            
    }




    public function add_daily_summary_old()
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
        $date = $now->copy()->subDay()->toDateString();
        $EndOfDay = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 23:59:00', $timezone);
        $oneDayBefore = $now->copy()->subDays(2)->toDateString();
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
