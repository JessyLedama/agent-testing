<?php

namespace App\Livewire;

use App\Models\Music;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\View as MusicView;
use Livewire\Component;
use Livewire\WithPagination;

class UserStreaming extends Component
{
    use WithPagination;

    public $selectedMusic = null;
    public $categoryFilter = null;
    public $searchQuery = '';
    public $comment = '';
    public $showAddToPlaylist = false;

    public function mount()
    {
        $musicId = request()->query('music');
        if ($musicId) {
            $this->playMusic($musicId);
        }
    }

    public function playMusic($musicId)
    {
        $this->selectedMusic = Music::with(['artist', 'category', 'comments.user', 'likes'])
            ->findOrFail($musicId);

        // Record view
        MusicView::create([
            'music_id' => $musicId,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function toggleLike($musicId, $isLike = true)
    {
        $existing = Like::where('user_id', auth()->id())
            ->where('music_id', $musicId)
            ->first();

        if ($existing) {
            if ($existing->is_like == $isLike) {
                $existing->delete();
            } else {
                $existing->update(['is_like' => $isLike]);
            }
        } else {
            Like::create([
                'user_id' => auth()->id(),
                'music_id' => $musicId,
                'is_like' => $isLike,
            ]);
        }

        if ($this->selectedMusic && $this->selectedMusic->id == $musicId) {
            $this->selectedMusic->refresh();
        }
    }

    public function addComment()
    {
        $this->validate([
            'comment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'music_id' => $this->selectedMusic->id,
            'content' => $this->comment,
        ]);

        $this->comment = '';
        $this->selectedMusic->refresh();
        session()->flash('comment_success', 'Comment added successfully!');
    }

    public function toggleFollow($artistId)
    {
        $existing = Follow::where('follower_id', auth()->id())
            ->where('artist_id', $artistId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Follow::create([
                'follower_id' => auth()->id(),
                'artist_id' => $artistId,
            ]);
        }

        if ($this->selectedMusic) {
            $this->selectedMusic->refresh();
        }
    }

    public function isFollowing($artistId)
    {
        return Follow::where('follower_id', auth()->id())
            ->where('artist_id', $artistId)
            ->exists();
    }

    public function getUserLike($musicId)
    {
        $like = Like::where('user_id', auth()->id())
            ->where('music_id', $musicId)
            ->first();

        return $like ? ($like->is_like ? 'like' : 'dislike') : null;
    }

    public function render()
    {
        $query = Music::published()
            ->with(['artist', 'category'])
            ->when($this->categoryFilter, function($q) {
                $q->where('category_id', $this->categoryFilter);
            })
            ->when($this->searchQuery, function($q) {
                $q->where('title', 'like', '%' . $this->searchQuery . '%')
                  ->orWhereHas('artist', function($q) {
                      $q->where('name', 'like', '%' . $this->searchQuery . '%');
                  });
            });

        $allMusic = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('livewire.user-streaming', [
            'allMusic' => $allMusic,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
