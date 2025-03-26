<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Signature extends Model
{
    use HasFactory;
    protected $fillable = ['sign'];

    protected static function booted()
    {
        static::created(function ($signature) {
            $signature->deleteOldSignatures();
        });
    }

    public function deleteOldSignatures()
    {
        $yesterday = Carbon::now()->subDay()->toDateString();
        self::whereDate('created_at', '<', $yesterday)->delete();
    }
}
