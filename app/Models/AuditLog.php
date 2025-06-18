<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'description',
        'module_id',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class);
    }

}
