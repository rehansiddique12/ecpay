<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = 'blacklists';
    protected $fillable = [
        'member_id',
        'reason',
    ];
} 