<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'from',
        'to'
    ];

    // If you need to cast the date fields
    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
    ];

    public function user()
{
    return $this->belongsTo(Admin::class);
}
}
