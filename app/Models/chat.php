<?php

namespace App\Models;

use App\Models\messagechat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'user1_id',
        'user2_id',

    ];
    public function participants(): HasMany
    {
        return $this->hasMany(messagechat::class, 'chat_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(messagechat::class, 'chat_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(messagechat::class, 'chat_id')->latest('updated_at');
    }


}
