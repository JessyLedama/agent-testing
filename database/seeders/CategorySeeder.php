<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::create(['name' => 'Pop', 'description' => 'Popular music']);
        \App\Models\Category::create(['name' => 'Rock', 'description' => 'Rock music']);
        \App\Models\Category::create(['name' => 'Hip Hop', 'description' => 'Hip hop and rap music']);
        \App\Models\Category::create(['name' => 'Classical', 'description' => 'Classical music']);
        \App\Models\Category::create(['name' => 'Jazz', 'description' => 'Jazz music']);
        \App\Models\Category::create(['name' => 'Electronic', 'description' => 'Electronic and dance music']);
    }
}
