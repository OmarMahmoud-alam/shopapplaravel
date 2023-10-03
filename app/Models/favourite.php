<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class favourite extends Model
{   protected $primaryKey = ['user_id', 'book_id'];
    public $incrementing = false;
    use HasFactory;
    protected $fillable = [
        'user_id',
        'book_id',
    ];
    /**
     * Get the books that owns the favourite
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function books(): BelongsTo
    {
        return $this->belongsTo(book::class, 'book_id', 'id');
    }
    
}
