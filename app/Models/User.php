<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\book;
use App\Models\rating;
use App\Models\favourite;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'state',
        'phone',
        'darkmode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(book::class, 'user_id', 'id');
    }
    public function putratings()
{
    return $this->hasMany(rating::class);
}
/*
 
public function favourites(): HasMany
{
    return $this->hasMany(favourite::class, 'user_id', 'id');
}
*/  
public function favourites(): BelongsToMany
{
    return $this->belongsToMany(book::class, 'favourites', 'user_id', 'book_id');
}
/**
 * The rating that belong to the User
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
public function rating(): BelongsToMany
{
    return $this->belongsToMany(Role::class, 'role_user_table', 'user_id', 'role_id');
}
}
