<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model
{
    use HasFactory;

    protected $table = 'partner_commissions';
    protected $fillable = [
        'id',
        'api_id',
        'from_id',
        'type',
        'amount',
        'charges',
        'total_amount',
        'charges_p',
        'profit',
        'profit_p',
        'transaction_id',
        'status',
        'created_at',
        'updated_at',
    ];

    public function api()
    {
        return $this->hasOne(Api::class, 'id', 'api_id');
    }

    public function fromapi()
    {
        return $this->hasOne(Api::class, 'id', 'from_id');
    }
}
