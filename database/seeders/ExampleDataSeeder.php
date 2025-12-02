<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users
        $regularUser = \App\Models\User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'is_artist' => false
        ]);

        $artist1 = \App\Models\User::factory()->create([
            'name' => 'John Artist',
            'email' => 'artist1@example.com',
            'is_artist' => true
        ]);

        $artist2 = \App\Models\User::factory()->create([
            'name' => 'Jane Musician',
            'email' => 'artist2@example.com',
            'is_artist' => true
        ]);

        // Get categories and statuses
        $popCategory = \App\Models\Category::where('name', 'Pop')->first();
        $rockCategory = \App\Models\Category::where('name', 'Rock')->first();
        $jazzCategory = \App\Models\Category::where('name', 'Jazz')->first();
        
        $draftStatus = \App\Models\Status::where('name', 'draft')->first();
        $publishedStatus = \App\Models\Status::where('name', 'published')->first();

        // Create published music from artist 1
        // Note: In the seeder, we manually add music to playlist.
        // In normal operation via API, this happens automatically in MusicController.
        $music1 = \App\Models\Music::create([
            'title' => 'Summer Vibes',
            'artist_id' => $artist1->id,
            'category_id' => $popCategory->id,
            'status_id' => $publishedStatus->id,
            'file_path' => 'music/summer-vibes.mp3',
            'duration' => 210
        ]);
        $artist1->playlist()->attach($music1->id);

        $music2 = \App\Models\Music::create([
            'title' => 'Rock Your World',
            'artist_id' => $artist1->id,
            'category_id' => $rockCategory->id,
            'status_id' => $publishedStatus->id,
            'file_path' => 'music/rock-your-world.mp3',
            'duration' => 245
        ]);
        $artist1->playlist()->attach($music2->id);

        // Create draft music from artist 1
        $music3 = \App\Models\Music::create([
            'title' => 'Work in Progress',
            'artist_id' => $artist1->id,
            'category_id' => $popCategory->id,
            'status_id' => $draftStatus->id,
            'file_path' => 'music/wip.mp3',
            'duration' => 180
        ]);
        $artist1->playlist()->attach($music3->id);

        // Create published music from artist 2
        $music4 = \App\Models\Music::create([
            'title' => 'Smooth Jazz Night',
            'artist_id' => $artist2->id,
            'category_id' => $jazzCategory->id,
            'status_id' => $publishedStatus->id,
            'file_path' => 'music/smooth-jazz.mp3',
            'duration' => 320
        ]);
        $artist2->playlist()->attach($music4->id);

        $music5 = \App\Models\Music::create([
            'title' => 'Midnight Blues',
            'artist_id' => $artist2->id,
            'category_id' => $jazzCategory->id,
            'status_id' => $publishedStatus->id,
            'file_path' => 'music/midnight-blues.mp3',
            'duration' => 280
        ]);
        $artist2->playlist()->attach($music5->id);
    }
}
