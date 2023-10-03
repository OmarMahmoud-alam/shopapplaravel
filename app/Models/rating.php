<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations;

class rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'seller_id',
        'rating',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}

}
