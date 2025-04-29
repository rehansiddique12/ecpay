<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    protected $fillable = [
        'roles_name',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
