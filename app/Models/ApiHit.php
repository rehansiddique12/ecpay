<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiHit extends Model
{
    use HasFactory;

    protected $table = 'api_hits';

    protected $fillable = [
        'e_wallet_name', 'acc_no',
    ];
}
