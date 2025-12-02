<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_can_list_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(6); // 6 categories seeded
    }

    public function test_can_view_single_category(): void
    {
        $category = Category::first();
        
        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name
            ]);
    }

    public function test_can_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Country',
            'description' => 'Country music'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Category created successfully',
                'category' => [
                    'name' => 'Country',
                    'description' => 'Country music'
                ]
            ]);
        
        $this->assertDatabaseHas('categories', [
            'name' => 'Country',
            'description' => 'Country music'
        ]);
    }

    public function test_can_update_category(): void
    {
        $category = Category::first();
        
        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
            'description' => 'Updated description'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully'
            ]);
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category'
        ]);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::first();
        
        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category deleted successfully'
            ]);
        
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }
}
