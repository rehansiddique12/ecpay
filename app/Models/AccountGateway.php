<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountGateway extends Model
{
    protected $table = 'account_gateways';

    protected $fillable = [
        'code',
        'name',
        'image',
        'status',
        'currencies',
        'currency',
        'symbol',
        'min_amount',
        'max_amount',
        'percentage_charge',
        'fixed_charge',
        'convention_rate',
        'minimum_withdrawal_amount',
        'maximum_withdrawal_amount',
        'parameters',
        'extra_parameters',
    ];

    protected $casts = [
        'currencies' => 'object',
        'parameters' => 'object',
        'extra_parameters' => 'object',
    ];

    public function scopeAutomatic()
    {
        return $query->where('id', '<', 1000);
    }

    public function scopeManual()
    {
        return $this->where('id', '>=', 1000);
    }
}
