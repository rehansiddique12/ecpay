<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPartnerSummaryLog extends Model
{
    use HasFactory;
    protected $table = 'daily_partner_summary_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'partner_id',
        'partner_balance',
        'payment_id',
        'total_amount',
        'summary_id',
        'closing_balance',
        'source',
        'created_at',
        'updated_at'
    ];
}
