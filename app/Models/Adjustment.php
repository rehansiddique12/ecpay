<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $table = 'adjustments';

    protected $fillable = ['id', 'month', 'adjustment', 'payment', 'payout', 'status', 'partner_id', 'created_at', 'updated_at'];

    public function api()
    {
        return $this->hasOne(Api::class, 'id', 'partner_id');
    }
}
