<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EWalletAccountTimeSlot extends Model
{
    use HasFactory;
    protected $table = 'e_wallet_account_time_slots';

    protected $fillable = [
        'e_wallet_account_id',
        'time_saved',
        'from_time',
        'to_time',
        'created_at',
        'updated_at'
    ];
}
