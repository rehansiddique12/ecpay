<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Authenticatable
{
    use Notifiable, HasRoles ,SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $guard_name = 'admin';

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



    // protected $fillable = [
    //     'name', 'username', 'email', 'phone', 'website', 'sign','status',
    //     'min_deposit', 'min_withdrawal', 'txn_verification',
    //     'api_endpoint_deposit', 'api_endpoint_withdrawal',
    //     'redirect_url', 'acc_type', 'password','api_key','admin_access','type'
    // ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'status',
        'website',
        'api_endpoint_deposit',
        'api_endpoint_withdrawal',
        'admin_access',
        'type',
        'api_key',
        'last_login',
        'remember_token',
        'balance',
        'min_deposit',
        'min_withdrawal',
        'acc_type',
        'parent_id',
        'sign',
        'secret_key',
        'txn_verification',
        'redirect_url',
        'timezone',
        'password_string',
        'category_id'
    ];



     public function commissions()
    {
        // return $this->hasOne(Commission::class, 'api_id', 'id');
    }

    public function parent()
{
    return $this->belongsTo(Api::class, 'parent_id');
}
}
