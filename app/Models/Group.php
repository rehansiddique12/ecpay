<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    public function accounts()
    {
        return $this->belongsToMany(EWalletAccount::class, 'account_groups', 'group_id', 'account_id');
    }

}
