<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $guarded = ['id'];
    protected $table = "funds";

    protected $casts = [
        'detail' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function api()
    {
        return $this->belongsTo(Api::class, 'api_id', 'id');
    }

     public function txn_record()
    {
        return $this->belongsTo(Txn::class, 'partner_transection_id', 'partner_transection_id');
    }
}
