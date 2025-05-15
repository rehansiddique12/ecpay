<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EWalletAccount extends Model
{
    protected $table = 'e_wallet_accounts';

    protected $fillable = [
        'e_wallet_name',
        'account_no',
        'account_type',
        'type',
        'balance',
        'live_balance',
        
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



}
