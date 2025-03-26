<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'date_time',
        'final_amount',
        'balance',
        'transection_type',
        'transection_id',
        'partner_id',
        'source',
    ];

    public function api()
    {
        return $this->belongsTo(Api::class, 'partner_id', 'id');
    }
}
