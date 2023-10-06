<?php

namespace App\Models;

use App\Models\chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class messagechat extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'reciever_id',
        'message',
        'chat_id',

    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id','id');
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(chat::class, 'chat_id');
    }
}
