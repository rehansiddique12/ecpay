<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerLog extends Model
{
    protected $table = 'partner_logs';

    protected $fillable = [
        'api_id',
        'log',
        'ip_address',
    ];
    
    public function api()
    {
        return $this->hasOne(Api::class, 'id', 'api_id');
    }
}
