<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'e_wallet_name',
        'amount',
        'user_account_no',
        'txn_id',
        'date',
        'time',
        'date_time',
        'transaction_type',
        'e_wallet_phone_number',
        'ip_address',
        'e_wallet_type',
        'mac_address',
        'payout_log_id',
        'status',
    ];

    // Add any relationships or additional methods here
}
