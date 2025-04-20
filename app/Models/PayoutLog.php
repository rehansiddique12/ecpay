<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutLog extends Model
{
    protected $guarded = ['id'];
    protected $table = 'payout_logs';

    protected $casts = [
        'information' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function method()
    {
        return $this->belongsTo(PayoutMethod::class, 'method_id');
    }
    
    public function payout()
    {
        return $this->belongsTo(Payout::class, 'payout_id');
    }

}
