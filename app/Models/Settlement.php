<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $table = 'settlements';

    protected $fillable = [
        'source',
        'source_name',
        'account_no',
        'amount',
        'charges',
        'net_amount',
        'partner_id',
        'status',
    ];

    public function api()
    {
        return $this->hasOne(Api::class, 'id', 'partner_id');
    }
}
