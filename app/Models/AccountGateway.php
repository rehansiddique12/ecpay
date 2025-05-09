<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountGateway extends Model
{
    protected $table = 'account_gateways';

    protected $fillable = [
        'code',
        'name',
        'currency',
        'symbol',
        'convention_rate',
        'withdraw_convention_rate',
        'min_amount',
        'max_amount',
        'minimum_withdrawal_amount',
        'maximum_withdrawal_amount',
        'fixed_deposit_charge',
        'percentage_deposit_charge',
        'fixed_withdraw_charge',
        'percentage_withdraw_charge',
        'daily_withdraw_limit',
        'monthly_withdraw_limit',
        'daily_deposit_limit',
        'monthly_deposit_limit',
        'parameters',
        'status',
        'note',
        'image', 
        'currencies',
        'sort_by',
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
