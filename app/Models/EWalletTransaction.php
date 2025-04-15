<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EWalletTransaction extends Model
{
    protected $table = 'e_wallet_transections';

    protected $fillable = [
        'from_e_wallet',
        'from_account_no',
        'to_e_wallet',
        'to_account_no',
        'amount',
        'txn_id',
        'date',
        'time',
        'date_time',
        'ip_address',
        'mac_address',
        'status',
        'transaction_date',
    ];
}
