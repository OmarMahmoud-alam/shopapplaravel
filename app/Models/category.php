<?php

namespace App\Models;

use App\Models\book;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',

    ];

/**
 * The roles that belong to the category
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
public function books(): BelongsToMany
{
    return $this->belongsToMany(book::class, 'book_categories', 'category_id', 'book_id');
}
    /**
     * Get the user that owns the category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

}
