<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EWalletLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'previous_balance',
        'amount',
        'charge',
        'commission',
        'final_amount',
        'balance',
        'transaction_type',
        'transaction_id',
        'source'
    ];

    // public function eWalletLog()
    // {
    //     return $this->hasOne(EWalletLog::class, 'transaction_id', 'id')
    //                 ->where('transaction_type', 1);
    // }

    // public function eWalletLog()
    // {
    //     return $this->hasOne(EWalletLog::class, 'transaction_id', 'id')
    //                 ->where('transaction_type', 2);
    // }

}
