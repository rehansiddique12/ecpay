<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentCommission extends Model
{
    use HasFactory;

    protected $table = 'parent_commissions';
   protected $fillable = [
    'user_id',
    'from_amount',
    'to_amount',
    'deposit_percentage',
    'withdrawal_percentage',
    'gateway_id',
    'parent_id',
    'type',
    'commission_id',
];

public function category()
{
    return $this->belongsTo(Category::class);
}

public function partner()
{
    return $this->belongsTo(Api::class, 'parent_id');
}

}
