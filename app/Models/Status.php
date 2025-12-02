<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = ['name'];

    /**
     * Get the music with this status.
     */
    public function music(): HasMany
    {
        return $this->hasMany(Music::class);
    }
}
