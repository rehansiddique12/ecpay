<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CCategory extends Model
{
    use HasFactory;

    // Table name (optional if different from plural of model name)
    protected $table = 'c_categories';

    // Fields that are mass assignable
    protected $fillable = [
        'title',
        'status'
    ];

    // Optional: Cast status to boolean
    protected $casts = [
        'status' => 'boolean',
    ];
}
