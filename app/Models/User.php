<?php

namespace App\Models;

use App\Models\book;
use App\Models\rating;
use App\Models\favourite;
use App\Notifications\messagesentnotification;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'addresse_id',
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


/**
 * Get all of the eventcomment for the User
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function eventcomment(): HasMany
{
    return $this->hasMany(eventComment::class, 'user_id', 'id');
}
/**
 * Get all of the eventcomment for the User
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function eventinterest(): HasMany
{
    return $this->hasMany(eventInterest::class, 'user_id', 'id');
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
public function sendPasswordResetNotification($token,)
{
    $email=$this->email; 
    $url = 'https://spa.test/reset-password?token=' . $token.'&email='.$email;

    $this->notify(new ResetPasswordNotification($url));
}
public function routeNotificationForOneSignal() : array{
    return ['tags'=>['key'=>'userId','relation'=>'=', 'value'=>(string)($this->id)]];
}
public function sendNewMessageNotification(array $data) : void {
    $this->notify(new messagesentnotification($data));
}
}
