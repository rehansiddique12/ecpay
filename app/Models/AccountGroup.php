<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'group_id',
    ];
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function account()
    {
        return $this->belongsTo(EWalletAccount::class, 'account_id');
    }
}
