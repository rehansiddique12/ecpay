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
        'balance',
         'live_balance'
    ];

    public function apiHits()
    {
        return $this->hasOne(ApiHit::class, 'acc_no', 'account_no');
    }



}
