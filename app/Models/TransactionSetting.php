<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionSetting extends Model
{
    protected $fillable = [
        'from_time',
        'to_time',
        'transfer_from_time',
        'transfer_to_time'
        ];
}
