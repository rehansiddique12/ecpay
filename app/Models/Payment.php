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

     public function txn_record()
    {
        return $this->belongsTo(Txn::class, 'partner_transection_id', 'partner_transection_id');
    }

    public function eWalletAccount()
    {
        return $this->hasOne(EWalletAccount::class, 'account_no', 'e_wallet_phone_number');
    }

    public function eWalletLog()
    {
        return $this->hasOne(EWalletLog::class, 'transaction_id', 'id')
                    ->where('transaction_type', 1);
    }
}
