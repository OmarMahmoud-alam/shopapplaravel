<?php

namespace App\Models;

use App\Models\book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Addresse extends Model
{
    use HasFactory;
    protected $fillable=[
        'long',
        'lat',
        'user_id',

    ];
    /**
     * Get all of the Books for the Addresse
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Books(): HasMany
    {
        return $this->hasMany(book::class,  'addresse_id', 'id');
    }

}
