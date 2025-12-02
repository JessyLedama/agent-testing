<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_artist',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_artist' => 'boolean',
        ];
    }

    /**
     * Get the music uploaded by this artist.
     */
    public function music()
    {
        return $this->hasMany(Music::class, 'artist_id');
    }

    /**
     * Get the music in this user's playlist.
     */
    public function playlist()
    {
        return $this->belongsToMany(Music::class, 'artist_playlist');
    }

    /**
     * Check if user is an artist.
     */
    public function isArtist(): bool
    {
        return $this->is_artist;
    }

    /**
     * Make user an artist.
     */
    public function becomeArtist(): void
    {
        $this->is_artist = true;
        $this->save();
    }
}
