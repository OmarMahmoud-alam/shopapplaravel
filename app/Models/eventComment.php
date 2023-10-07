<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class eventComment extends Model
{
    use HasFactory;
    protected $table = 'event_comments';

    protected $fillable = [
        'user_id',
        'event_id',
        'comment',
    ];
    /**
     * Get the user that owns the eventComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function event(): BelongsTo
    {
        return $this->belongsTo(event::class, 'event_id', 'id');
    }

}
