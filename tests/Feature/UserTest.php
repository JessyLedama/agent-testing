<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_user_can_become_artist(): void
    {
        $user = User::factory()->create(['is_artist' => false]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/become-artist');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'You are now an artist!'
            ]);
        
        $this->assertTrue($user->fresh()->isArtist());
    }

    public function test_artist_cannot_become_artist_again(): void
    {
        $user = User::factory()->create(['is_artist' => true]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/become-artist');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'User is already an artist'
            ]);
    }

    public function test_guest_cannot_become_artist(): void
    {
        $response = $this->postJson('/api/user/become-artist');

        $response->assertStatus(401);
    }
}
