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
    public function users1(): HasOne
    {
        return $this->hasOne(user::class, 'id','user1_id');
    }
    public function users2(): HasOne
    {
        return $this->hasOne(user::class, 'id','user2_id');
    }
    public function lastMessage(): HasMany
    {
      /*  return $this->hasMany(messagechat::class,'chat_id','id')->latest('created_at')->simplePaginate(
            1,
            ['*'],
            'page',
            1
        );*/
    }


}
