<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Music;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
        Storage::fake('public');
    }

    public function test_artist_can_upload_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $status = Status::where('name', 'draft')->first();
        
        $file = UploadedFile::fake()->create('song.mp3', 1000);
        
        $response = $this->actingAs($artist, 'sanctum')
            ->postJson('/api/music', [
                'title' => 'My First Song',
                'category_id' => $category->id,
                'status_id' => $status->id,
                'file' => $file,
                'duration' => 180
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Music uploaded successfully'
            ]);
        
        $this->assertDatabaseHas('music', [
            'title' => 'My First Song',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $status->id
        ]);
        
        // Check that music was added to artist's playlist
        $music = Music::where('title', 'My First Song')->first();
        $this->assertTrue($artist->playlist->contains($music));
    }

    public function test_non_artist_cannot_upload_music(): void
    {
        $user = User::factory()->create(['is_artist' => false]);
        $category = Category::first();
        $status = Status::where('name', 'draft')->first();
        
        $file = UploadedFile::fake()->create('song.mp3', 1000);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/music', [
                'title' => 'My Song',
                'category_id' => $category->id,
                'status_id' => $status->id,
                'file' => $file
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Only artists can upload music'
            ]);
    }

    public function test_guest_can_view_published_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $publishedStatus = Status::where('name', 'published')->first();
        
        $music = Music::create([
            'title' => 'Public Song',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $publishedStatus->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $response = $this->getJson('/api/music');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Public Song'
            ]);
    }

    public function test_guest_cannot_view_draft_music_in_list(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $draftStatus = Status::where('name', 'draft')->first();
        
        Music::create([
            'title' => 'Draft Song',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $draftStatus->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $response = $this->getJson('/api/music');

        $response->assertStatus(200)
            ->assertJsonMissing([
                'title' => 'Draft Song'
            ]);
    }

    public function test_owner_can_view_own_draft_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $draftStatus = Status::where('name', 'draft')->first();
        
        $music = Music::create([
            'title' => 'My Draft',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $draftStatus->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $response = $this->actingAs($artist, 'sanctum')
            ->getJson("/api/music/{$music->id}");

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'My Draft'
            ]);
    }

    public function test_non_owner_cannot_view_draft_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $otherUser = User::factory()->create();
        $category = Category::first();
        $draftStatus = Status::where('name', 'draft')->first();
        
        $music = Music::create([
            'title' => 'Private Draft',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $draftStatus->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/music/{$music->id}");

        $response->assertStatus(403);
    }

    public function test_artist_can_update_own_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $status = Status::where('name', 'draft')->first();
        
        $music = Music::create([
            'title' => 'Original Title',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $publishedStatus = Status::where('name', 'published')->first();
        
        $response = $this->actingAs($artist, 'sanctum')
            ->putJson("/api/music/{$music->id}", [
                'title' => 'Updated Title',
                'status_id' => $publishedStatus->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Music updated successfully'
            ]);
        
        $this->assertDatabaseHas('music', [
            'id' => $music->id,
            'title' => 'Updated Title',
            'status_id' => $publishedStatus->id
        ]);
    }

    public function test_artist_cannot_update_others_music(): void
    {
        $artist1 = User::factory()->create(['is_artist' => true]);
        $artist2 = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $status = Status::where('name', 'draft')->first();
        
        $music = Music::create([
            'title' => 'Artist 1 Song',
            'artist_id' => $artist1->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $response = $this->actingAs($artist2, 'sanctum')
            ->putJson("/api/music/{$music->id}", [
                'title' => 'Hacked Title'
            ]);

        $response->assertStatus(403);
    }

    public function test_artist_can_delete_own_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $status = Status::where('name', 'draft')->first();
        
        $music = Music::create([
            'title' => 'To Be Deleted',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
            'file_path' => 'music/test.mp3',
            'duration' => 200
        ]);
        
        $response = $this->actingAs($artist, 'sanctum')
            ->deleteJson("/api/music/{$music->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Music deleted successfully'
            ]);
        
        $this->assertDatabaseMissing('music', [
            'id' => $music->id
        ]);
    }

    public function test_artist_can_view_own_music(): void
    {
        $artist = User::factory()->create(['is_artist' => true]);
        $category = Category::first();
        $draftStatus = Status::where('name', 'draft')->first();
        $publishedStatus = Status::where('name', 'published')->first();
        
        // Create draft and published music
        Music::create([
            'title' => 'Draft Song',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $draftStatus->id,
            'file_path' => 'music/draft.mp3',
            'duration' => 150
        ]);
        
        Music::create([
            'title' => 'Published Song',
            'artist_id' => $artist->id,
            'category_id' => $category->id,
            'status_id' => $publishedStatus->id,
            'file_path' => 'music/published.mp3',
            'duration' => 200
        ]);
        
        $response = $this->actingAs($artist, 'sanctum')
            ->getJson('/api/music?my_music=1');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Draft Song'])
            ->assertJsonFragment(['title' => 'Published Song']);
    }
}
