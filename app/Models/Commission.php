<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commissions';

    protected $fillable = [
        'from_amount',
        'to_amount',
        'deposit_percentage',
        'withdrawal_percentage',
        'settlement_percentage',
        'api_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Commission::class, 'parent_id');
    }
    
    
}
