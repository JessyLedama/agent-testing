<?php

namespace App\Livewire;

use App\Models\Music;
use Livewire\Component;

class LandingPage extends Component
{
    public function render()
    {
        // Get random published music - different on each page load
        $randomMusic = Music::published()
            ->with(['artist', 'category'])
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('livewire.landing-page', [
            'randomMusic' => $randomMusic
        ])->layout('layouts.app');
    }
}
