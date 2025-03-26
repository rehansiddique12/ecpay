<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPartnerSummary extends Model
{
    use HasFactory;
    protected $table = 'daily_partner_summary';
    protected $fillable = ['id', 'api_id', 'closing_balance', 'actual_balance', 'created_at', 'updated_at'];
}
