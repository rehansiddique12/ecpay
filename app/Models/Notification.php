<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // Only if your table name is misspelled

    protected $fillable = [
        'ewallet_account_id',
        'user_id'
    ];

    public function ewalletAccount()
    {
        return $this->belongsTo(EWalletAccount::class, 'ewallet_account_id');
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
