<?php

namespace App\Livewire;

use App\Models\Playlist;
use App\Models\Music;
use Livewire\Component;

class PlaylistManager extends Component
{
    public $playlists;
    public $name;
    public $description;
    public $editingId = null;
    public $selectedPlaylistId = null;
    public $selectedPlaylistMusic = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->loadPlaylists();
    }

    public function loadPlaylists()
    {
        $this->playlists = Playlist::where('user_id', auth()->id())
            ->withCount('music')
            ->latest()
            ->get();
    }

    public function create()
    {
        $this->validate();

        Playlist::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->reset(['name', 'description']);
        $this->loadPlaylists();
        session()->flash('message', 'Playlist created successfully!');
    }

    public function edit($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $id;
        $this->name = $playlist->name;
        $this->description = $playlist->description;
    }

    public function update()
    {
        $this->validate();

        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($this->editingId);
        $playlist->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->reset(['editingId', 'name', 'description']);
        $this->loadPlaylists();
        session()->flash('message', 'Playlist updated successfully!');
    }

    public function delete($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($id);
        $playlist->delete();
        $this->loadPlaylists();
        session()->flash('message', 'Playlist deleted successfully!');
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'name', 'description']);
    }

    public function viewPlaylist($id)
    {
        $this->selectedPlaylistId = $id;
        $playlist = Playlist::with('music.artist')->findOrFail($id);
        $this->selectedPlaylistMusic = $playlist->music;
    }

    public function removeFromPlaylist($playlistId, $musicId)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($playlistId);
        $playlist->music()->detach($musicId);
        $this->viewPlaylist($playlistId);
        session()->flash('message', 'Song removed from playlist!');
    }

    public function addToPlaylist($playlistId, $musicId)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($playlistId);
        
        if (!$playlist->music()->where('music_id', $musicId)->exists()) {
            $playlist->music()->attach($musicId);
            session()->flash('message', 'Song added to playlist!');
        } else {
            session()->flash('error', 'Song already in playlist!');
        }
        
        $this->loadPlaylists();
    }

    public function render()
    {
        $availableMusic = Music::published()
            ->with('artist')
            ->latest()
            ->get();

        return view('livewire.playlist-manager', [
            'availableMusic' => $availableMusic,
        ])->layout('layouts.app');
    }
}
