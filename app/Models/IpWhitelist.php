<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class IpWhitelist extends Model
{
    protected $table = 'ipwhitelist';

    // only include real columns
    protected $fillable = ['ip_address', 'user_id'];

    // relationship to admins table
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
