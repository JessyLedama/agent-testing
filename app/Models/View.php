<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class View extends Model
{
    protected $fillable = [
        'music_id',
        'user_id',
        'ip_address',
    ];

    /**
     * Get the music that was viewed.
     */
    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class);
    }

    /**
     * Get the user who viewed the music.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
