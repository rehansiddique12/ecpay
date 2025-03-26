<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AccountLog extends Model
{
    protected $table = 'account_logs';

    protected $fillable = ['id', 'amount', 'type', 'e_wallet_account_id', 'created_at', 'updated_at'];

    public function e_wallet_account()
    {
        return $this->hasOne(EWalletAccount::class, 'id', 'e_wallet_account_id');
    }
}
