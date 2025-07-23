<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistRemoval extends Model
{
    protected $table = 'blacklist_removals';
    protected $fillable = [
        'member_id',
        'removed_at',
        'admin_id',
        'reason',
    ];
    public $timestamps = true;
} 