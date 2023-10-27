<?php

namespace App\Models;

use App\Models\photo;
use App\Models\Addresse;
use App\Models\category;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use League\Flysystem\Filesystem;
use Illuminate\Support\Arr;
class book extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'price',
        'addresse_id',
        //مضفنهاش فى الباك اند
        'author',

        'discription',
    ];

       /**
         * The roles that belong to the book
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */

        public function categories(): BelongsToMany
        {
            return $this->belongsToMany(category::class, 'book_categories', 'book_id', 'category_id');
        }
 

        /**
         * Get the addresses that owns the book
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function addresses(): BelongsTo
        {
            return $this->belongsTo(Addresse::class, 'addresse_id', 'id');
        }
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Get all of the favourites for the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
  /*  public function favourites(): HasMany
    {
        return $this->hasMany(Comment::class, 'foreign_key', 'local_key');
    }*/
    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourites', 'book_id', 'user_id');
    }
    public function photos()
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
