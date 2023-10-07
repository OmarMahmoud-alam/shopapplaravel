<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'place_link',
        'name',
        'discription',
        'startat',
        'endedat',
        'online',

    ];
    /**
     * Get all of the eventcomment for the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eventcomment(): HasMany
    {
        return $this->hasMany(eventComment::class, 'event_id', 'id');
    }
    
    public function eventinterest(): HasMany
    {
        return $this->hasMany(eventInterest::class, 'event_id', 'id');
    }


}
