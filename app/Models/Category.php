<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Get the music in this category.
     */
    public function music(): HasMany
    {
        return $this->hasMany(Music::class);
    }
}
