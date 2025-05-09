<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    protected $fillable = [
        'name',
        'used_for',
        'admin_access',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
