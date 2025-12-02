<?php

namespace App\Livewire;

use App\Models\Music;
use App\Models\Category;
use App\Models\Status;
use App\Notifications\NewMusicReleased;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArtistDashboard extends Component
{
    use WithFileUploads;

    public $title;
    public $category_id;
    public $status_id;
    public $file_path;
    public $duration;
    public $editingId = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'status_id' => 'required|exists:statuses,id',
        'file_path' => 'nullable|file|mimes:mp3,wav,ogg,flac|max:20480',
        'duration' => 'nullable|integer',
    ];

    public function mount()
    {
        if (!auth()->user()->is_artist) {
            return redirect('/')->with('error', 'Only artists can access this page');
        }
    }

    public function save()
    {
        $this->validate();

        $filePath = null;
        if ($this->file_path) {
            $filePath = $this->file_path->store('music', 'public');
        }

        $music = Music::create([
            'title' => $this->title,
            'artist_id' => auth()->id(),
            'category_id' => $this->category_id,
            'status_id' => $this->status_id,
            'file_path' => $filePath,
            'duration' => $this->duration,
        ]);

        // Notify followers if music is published
        if ($music->status->name === 'published') {
            $followers = auth()->user()->followers;
            foreach ($followers as $follower) {
                $follower->notify(new NewMusicReleased($music));
            }
        }

        session()->flash('message', 'Music uploaded successfully!');
        $this->reset(['title', 'category_id', 'status_id', 'file_path', 'duration']);
    }

    public function edit($id)
    {
        $music = Music::where('artist_id', auth()->id())->findOrFail($id);
        $this->editingId = $id;
        $this->title = $music->title;
        $this->category_id = $music->category_id;
        $this->status_id = $music->status_id;
        $this->duration = $music->duration;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status_id' => 'required|exists:statuses,id',
            'duration' => 'nullable|integer',
        ]);

        $music = Music::where('artist_id', auth()->id())->findOrFail($this->editingId);
        
        $music->update([
            'title' => $this->title,
            'category_id' => $this->category_id,
            'status_id' => $this->status_id,
            'duration' => $this->duration,
        ]);

        session()->flash('message', 'Music updated successfully!');
        $this->reset(['editingId', 'title', 'category_id', 'status_id', 'duration']);
    }

    public function delete($id)
    {
        $music = Music::where('artist_id', auth()->id())->findOrFail($id);
        $music->delete();
        session()->flash('message', 'Music deleted successfully!');
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'title', 'category_id', 'status_id', 'file_path', 'duration']);
    }

    public function render()
    {
        $myMusic = Music::where('artist_id', auth()->id())
            ->with(['category', 'status'])
            ->latest()
            ->get();

        $categories = Category::all();
        $statuses = Status::all();

        return view('livewire.artist-dashboard', [
            'myMusic' => $myMusic,
            'categories' => $categories,
            'statuses' => $statuses,
        ])->layout('layouts.app');
    }
}
