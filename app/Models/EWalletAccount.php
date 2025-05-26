<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EWalletAccount extends Model
{
    protected $table = 'e_wallet_accounts';

    protected $fillable = [
        'e_wallet_name',
        'account_no',
        'type',
        'account_type',
        'daily_limit',
        'monthly_limit',
        'daily_limit_transaction',
        'monthly_limit_transaction',
        'daily_limit_withdrawal',
        'monthly_limit_withdrawal',
        'daily_limit_withdrawal_transaction',
        'monthly_limit_withdrawal_transaction',
        'deposit_daily_limit_percentage',
        'withdrawal_daily_limit_percentage',
        'deposit_monthly_limit_percentage',
        'withdrawal_monthly_limit_percentage',
        'max_transaction_per_minute',
        'max_amount_per_minute',
        'low_balance_amount',
        'apply_time_limit',
        'image',
        'status',
        'balance',
        'live_balance',
        'daily_received',
        'monthly_received',
        'daily_sent',
        'monthly_sent',
        'send',
        'received',
        'device_name',
        'location_id',
        // Note: 'account_group' is handled via relationship, not mass assignment
        // Note: 'time_slots' is commented out in your code
    ];

    public function apiHits()
    {
        return $this->hasOne(ApiHit::class, 'acc_no', 'account_no');
    }

    public function category()
    {
        return $this->belongsTo(CCategory::class);
    }

    public function group()
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id');
    }
    public function location()
    {
        return $this->belongsTo(UserLocation::class, 'location_id');
    }
}
