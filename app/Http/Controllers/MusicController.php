<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // If user is authenticated and wants their own music
        if ($request->has('my_music') && $user) {
            $music = Music::with(['category', 'status', 'artist'])
                ->where('artist_id', $user->id)
                ->get();
        } else {
            // Show only published music to public
            $music = Music::with(['category', 'status', 'artist'])
                ->published()
                ->get();
        }
        
        return response()->json($music);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isArtist()) {
            return response()->json([
                'message' => 'Only artists can upload music'
            ], 403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status_id' => 'required|exists:statuses,id',
            'file' => 'required|file|mimes:mp3,wav,ogg,flac|max:' . (20 * 1024), // Max 20MB
            'duration' => 'nullable|integer'
        ]);
        
        $filePath = $request->file('file')->store('music', 'public');
        
        $music = Music::create([
            'title' => $request->title,
            'artist_id' => $user->id,
            'category_id' => $request->category_id,
            'status_id' => $request->status_id,
            'file_path' => $filePath,
            'duration' => $request->duration
        ]);
        
        // Add to artist's playlist automatically
        $user->playlist()->attach($music->id);
        
        return response()->json([
            'message' => 'Music uploaded successfully',
            'music' => $music->load(['category', 'status', 'artist'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Music $music)
    {
        $this->authorize('view', $music);
        
        return response()->json($music->load(['category', 'status', 'artist']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Music $music)
    {
        $this->authorize('update', $music);
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'status_id' => 'sometimes|exists:statuses,id',
            'duration' => 'nullable|integer'
        ]);
        
        $music->update($request->only(['title', 'category_id', 'status_id', 'duration']));
        
        return response()->json([
            'message' => 'Music updated successfully',
            'music' => $music->load(['category', 'status', 'artist'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Music $music)
    {
        $this->authorize('delete', $music);
        
        // Delete the file
        if (Storage::disk('public')->exists($music->file_path)) {
            Storage::disk('public')->delete($music->file_path);
        }
        
        $music->delete();
        
        return response()->json([
            'message' => 'Music deleted successfully'
        ]);
    }
}
