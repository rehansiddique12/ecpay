<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyEWalletSummary extends Model
{
    use HasFactory;
    protected $table = 'daily_ewallet_summary';

    protected $fillable = [
        'id',
        'e_wallet_id',
        'closing_balance',
        'actual_balance',
        'created_at',
        'updated_at',
    ];
}
