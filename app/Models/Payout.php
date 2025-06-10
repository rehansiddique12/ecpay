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
        'check_by'
    ];

    // Add any relationships or additional methods here
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

    public function api()
    {
        return $this->belongsTo(Api::class, 'api_id', 'id');
    }

    public function eWalletAccount()
    {
        return $this->hasOne(EWalletAccount::class, 'account_no', 'e_wallet_phone_number');
    }

    //  public function method()
    // {
    //     return $this->belongsTo(PayoutMethod::class, 'method_id');
    // }
}
