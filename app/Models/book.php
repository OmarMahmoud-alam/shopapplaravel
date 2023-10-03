<?php

namespace App\Models;

use App\Models\Addresse;
use App\Models\category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
