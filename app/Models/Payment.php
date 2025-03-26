<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'e_wallet_name',
        'amount',
        'sender',
        'txn_id',
        'date',
        'time',
        'date_time',
        'transaction_type',
        'e_wallet_phone_number',
        'ip_address',
        'e_wallet_type',
        'source',
        'mac_address',
        'status',
        'transaction_id',
        'e_wallet_charges',
    ];

    // Add any relationships or additional methods here
}
