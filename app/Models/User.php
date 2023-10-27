<?php

namespace App\Models;

use App\Models\book;
use App\Models\rating;
use App\Models\favourite;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Notifications\messagesentnotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
    public function bookswithimage(): HasMany
    {
        return $this->books->with('getfirsturl');
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
public function photos():MorphOne
{
    return $this->MorphOne(photo::class,'photoable');
}
/*
public function getprofileimageurlAttribute(){
    if($this->photos !=null){
        return Storage::disk('imagesfp')->url($this->photos->src);

    }
    else{
        return null;
    }
}

*/



public function getuserimagesrc(){
   $src= $this->photos()->first('src');
    if(!$src){
        return null;
    }
    return $this->photos()->first('src');
}
public function getprofileimage(){
    $src=$this->getuserimagesrc();
    if(!$src){
        return null;
    }
    $result=Storage::disk('imagesfp')->url($src['src']);
    return $result;
}



}
