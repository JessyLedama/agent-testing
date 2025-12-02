<?php

namespace App\Policies;

use App\Models\Music;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MusicPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view published music
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Music $music): bool
    {
        // Published music is available to everyone
        if ($music->status->name === 'published') {
            return true;
        }
        
        // Draft music only available to owner
        return $user && $music->artist_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isArtist();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Music $music): bool
    {
        return $user->id === $music->artist_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Music $music): bool
    {
        return $user->id === $music->artist_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Music $music): bool
    {
        return $user->id === $music->artist_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Music $music): bool
    {
        return $user->id === $music->artist_id;
    }
}
