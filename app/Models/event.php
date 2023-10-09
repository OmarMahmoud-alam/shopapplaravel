<?php

namespace App\Models;

use App\Models\photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    public function photos():MorphMany
    {
        return $this->morphMany(photo::class,'photoable');
    }

    public function getbookimagesurlAttribute($src){
        if (is_object($src)) {

            $urls = [];
            foreach ($src  as $source) {

                $urls[] = Storage::disk('imagesfp')->url($source);
            }
            return $urls;
        } else {
            return[ Storage::disk('imagesfp')->url($src)];
        }
    }
    public function getbookfirstsrc(){
       $src= $this->photos()->first('src');
        if(!$src){
            return null;
        }
        return $this->photos()->first('src');
    }
    public function getfirsturl(){
        $src=$this->getbookfirstsrc();
        if(!$src){
            return null;
        }
        return $this->getbookimagesurlAttribute($src['src']);
    }
    public function getbookallsrc(){
      //  Log::info($this->photos()->pluck('photos.src'));
        return $this->photos()->pluck('photos.src');
    }
    public function getallurl(){
        return $this->getbookimagesurlAttribute($this->getbookallsrc());
    }

}
