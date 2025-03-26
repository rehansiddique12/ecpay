<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoStepVerification extends Model
{
    protected $table = 'two_step_verifications';

    protected $fillable = [
        'user_id',
        'g_auth_status',
        'g_secret_key',
        'otp_code',
    ];

    // If you want to disable timestamps (created_at and updated_at)
    public $timestamps = true;

    // Additional model configuration goes here
}
