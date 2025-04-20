<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_logs';

    protected $fillable = [
        'request_method',
        'request_url',
        'request_payload',
        'response_code',
        'response_payload',
        'request_headers',
        'response_headers',
    ];
}
