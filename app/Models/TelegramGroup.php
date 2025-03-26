<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramGroup extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'group_name',
        'group_username',
        'status',
        'api_id'
    ];
}
