<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiTransaction extends Model
{
    protected $table = 'api_transactions';

    protected $fillable = [
        'amount',
        'adjustment',
        'source',
        'txn',
        'partner_id',
        'charges',
        // 'created_at' and 'updated_at' are automatically managed by Laravel.
    ];

    public function api()
    {
        return $this->hasOne(Api::class, 'id', 'partner_id');
    }

    // You can also specify any relationships or additional methods here.
}
