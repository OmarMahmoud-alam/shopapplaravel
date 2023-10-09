<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class photo extends Model
{
    use HasFactory;
    protected $fillable = [
        'src', 'type'
    ];
    public function photoable(){
        return $this->morphTo();
    }
}
