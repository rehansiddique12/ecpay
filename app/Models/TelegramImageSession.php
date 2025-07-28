<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramImageSession extends Model
{
    protected $table = 'telegram_image_sessions';
    
    protected $fillable = [
        'message_id',
        'chat_id',
        'user_id',
        'partner_transection_id',
        'txn_id',
        'e_wallet_phone_number',
        'amount',
        'image_path',
        'ocr_text',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'status' => 'integer',
    ];
} 