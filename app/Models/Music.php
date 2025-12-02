<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Music extends Model
{
    protected $fillable = [
        'title',
        'artist_id',
        'category_id',
        'status_id',
        'file_path',
        'duration'
    ];

    /**
     * Get the artist that owns the music.
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    /**
     * Get the category of the music.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the status of the music.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get users who have this music in their playlist.
     */
    public function playlistUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'artist_playlist');
    }

    /**
     * Scope to only get published music.
     */
    public function scopePublished($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'published');
        });
    }

    /**
     * Scope to get music owned by a specific user.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('artist_id', $userId);
    }
}
