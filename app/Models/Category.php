<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'status',
    ];


    public function gateways()
    {
        return $this->hasMany(Gateway::class, 'category_id');
    }
}
