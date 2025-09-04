<?php

namespace App\Console\Commands;

use App\Models\Api;
use App\Models\Payout;
use App\Models\Setting;
use App\Models\Commission;
use App\Models\EWalletAccount;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\ParentCommission;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\Log;

class AssignAccountToPayout extends Command
{

    protected $signature = 'command:assign-account-to-payout';
    protected $description = 'Command description';

    public function handle()
    {
        Log::info('Run Daily AssignAccountToPayout Command');
        $allPayoutInfo = Payout::where('is_account_assign', 0)
            ->where('transfer_status',2)
            ->where('status', '!=','Complete')
            ->where('status', '!=','Reject')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->get();
        $Setting = Setting::where('name', 'last_account_active')->first();

        foreach ($allPayoutInfo as $payout) {

            $api_key = Api::select('id' , 'website' , 'category_id' , 'min_withdrawal' , 'balance')->where('id', $payout->api_id)->where('type', 'Admin')->first();
            $charge = 0;
            $source = "";
            if ($api_key) {
                $source = $api_key->website;
                $api_id = $api_key->id;
            }

            if ($api_key->min_withdrawal > $payout->amount) {
               continue;
            }

            $now = Carbon::now();
            $startOfToday = $now->copy()->startOfDay();
            $startOfMonth = $now->copy()->startOfMonth();
            $oneMinuteAgo = $now->copy()->subMinute();

            // Query
            $results = Payout::selectRaw('
                            e_wallet_phone_number,
                            COUNT(CASE WHEN created_at >= ? THEN 1 END) AS counts_for_round_robin,
                            COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS today_count,
                            COUNT(CASE WHEN completions_at >= ? AND status = "Complete" THEN 1 END) AS month_count,
                            COUNT(CASE WHEN created_at >= ? THEN 1 END) AS one_min_count,
                            SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) AS one_min_sum
                        ', [
                $Setting->value,
                $startOfToday,
                $startOfMonth,
                $oneMinuteAgo,
                $oneMinuteAgo
            ])
            ->where('e_wallet_name', $payout->e_wallet_name)
            ->whereNotNull('e_wallet_phone_number')
            ->where('e_wallet_phone_number', '!=', '')
            ->groupBy('e_wallet_phone_number')
            ->get();


            // ->where('created_at', '>=', $startOfMonth)

            $all_accounts = [];

            foreach ($results as $result) {
                $all_accounts[$result->e_wallet_phone_number]['counts_for_round_robin'] = $result->counts_for_round_robin;
                $all_accounts[$result->e_wallet_phone_number]['today_count'] = $result->today_count;
                $all_accounts[$result->e_wallet_phone_number]['month_count'] = $result->month_count;
                $all_accounts[$result->e_wallet_phone_number]['one_min_count'] = $result->one_min_count;
                $all_accounts[$result->e_wallet_phone_number]['one_min_sum'] = $result->one_min_sum;
            }

            $current_time = Carbon::now('Asia/Dhaka');
            $account = EWalletAccount::where('e_wallet_name', $payout->e_wallet_name)
                ->where('monthly_limit_withdrawal', '>', 'monthly_sent')
                ->whereRaw('daily_limit_withdrawal - daily_sent > ?', [$payout->amount])
                ->where('status', 1)
                ->where('max_withdrawal_amount', '>=', $payout->amount)
                ->whereIn('account_type', ['Withdrawal', 'Both'])
                ->with('timeSlots')
                ->get()
                ->filter(function ($single_account) use ($all_accounts, $current_time) {
                    $phone = $single_account->account_no;

                    // Check all transaction limits
                    $validTransactionLimits = !isset($all_accounts[$phone]) || (
                        $single_account->daily_limit_transaction > ($all_accounts[$phone]['today_count'] ?? 0) &&
                        $single_account->monthly_limit_transaction > ($all_accounts[$phone]['month_count'] ?? 0) &&
                        $single_account->max_transaction_per_minute_withdrawal > ($all_accounts[$phone]['one_min_count'] ?? 0) &&
                        $single_account->max_amount_per_minute > ($all_accounts[$phone]['one_min_sum'] ?? 0)
                    );

                    // Check if at least one time slot matches

                    $validTimeSlot = $single_account->timeSlots->contains(function ($slot) use ($current_time) {
                        $from = Carbon::parse($slot->from_time);
                        $to = Carbon::parse($slot->to_time === '00:00:00' ? '23:59:59' : $slot->to_time);

                        return $current_time->between($from, $to);
                    });


                    return $validTransactionLimits && $validTimeSlot;
                })
                ->sortBy(function ($single_account) use ($all_accounts) {
                    return $all_accounts[$single_account->account_no]['counts_for_round_robin'] ?? 0;
                })
                ->values()
                ->first();


            if (!$account) {
                continue;
            }

            if ($source != env('APP_WEBSITE')) {

                $sum = Payout::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->where('api_id', $api_id)
                    ->where('status', 'Complete')
                    ->sum('amount');

                if (!$sum) {
                    $sum = 0;
                }

                $commissions = Commission::where('category_id', $api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();

                if ($commissions) {
                    $charge = $commissions->withdrawal_percentage * $payout->amount / 100;
                } else {
                    $commissions = Commission::where('category_id', $api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                    if ($commissions) {
                        $charge = $commissions->withdrawal_percentage * $payout->amount / 100;
                    }
                }
            }

            $previous_pending = Payout::where('api_id', $api_key->id)
            ->where(function ($query) {
                $query->where('transfer_status', 1)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('transfer_status', 2)
                            ->where('status', 'Pending');
                    });
            })
            ->sum('amount');

            if ($payout->amount + $charge + $previous_pending > $api_key->balance) {
                continue;
            }

            $parentIds = ParentCommission::where('user_id', $api_key->id)
                ->pluck('parent_id')
                ->unique()
                ->values();

            if(isset($account->id)){
                foreach ($parentIds as  $parentId) {

                    $parent_charge = 0;

                    if (isset($commissions->id)) {
                        $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();

                        if ($parent_commission) {
                            $parent_charge = $parent_commission->withdrawal_percentage * $payout->amount / 100;
                        } else {
                            $parent_commission = ParentCommission::where('user_id', $api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                            if ($parent_commission) {
                                $parent_charge = $parent_commission->withdrawal_percentage * $payout->amount / 100;
                            }
                        }
                    }
                    $exist_partner_commision =  PartnerCommission::where('api_id', $api_key->id)->where('from_id', $parentId)->where('type', 2)->where('transaction_id', $payout->id)->first();
                    if(!$exist_partner_commision){
                        if ($parent_charge > 0) {
                            $PartnerCommission = new PartnerCommission();
                            $PartnerCommission->api_id = $api_key->id;
                            $PartnerCommission->from_id = $parentId;
                            $PartnerCommission->type = 2;
                            $PartnerCommission->amount = $payout->amount;
                            $PartnerCommission->charges = $charge;
                            $PartnerCommission->total_amount =  $payout->amount + $charge;
                            $PartnerCommission->charges_p = $commissions->withdrawal_percentage ?? 0;
                            $profit_p = $parent_commission->withdrawal_percentage;
                            $profit = $profit_p * $payout->amount / 100;
                            $PartnerCommission->profit = $profit;
                            $PartnerCommission->profit_p = $profit_p;
                            $PartnerCommission->transaction_id = $payout->id;
                            $PartnerCommission->status = 0;
                            $PartnerCommission->save();
                        }
                    }
                }
            }


            $payout->e_wallet_phone_number = $account->account_no;
            $payout->is_account_assign = 1;
            $payout->charge = $charge;
            $payout->e_wallet_type = $account->type;
            $payout->save();
        }
    }
}
