<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Api extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id'];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'admin_access' => 'object'
    ];

    public function siteNotificational()
    {
        // return $this->morphOne(SiteNotification::class, 'siteNotificational', 'site_notificational_type', 'site_notificational_id');
    }

    // Assuming you have a 'password_resets' table, you may need to specify it like this:
    protected $table = 'apis';



    protected $fillable = [
        'name', 'username', 'email', 'phone', 'website', 'sign','status',
        'min_deposit', 'min_withdrawal', 'txn_verification',
        'api_endpoint_deposit', 'api_endpoint_withdrawal',
        'redirect_url', 'acc_type', 'password','api_key','admin_access','type'
    ];


     public function commissions()
    {
        // return $this->hasOne(Commission::class, 'api_id', 'id');
    }
}
