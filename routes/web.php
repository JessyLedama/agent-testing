<?php

use App\Livewire\LandingPage;
use App\Livewire\ArtistDashboard;
use App\Livewire\UserStreaming;
use App\Livewire\PlaylistManager;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('home');

// Auth routes
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/streaming', UserStreaming::class)->name('streaming');
    Route::get('/playlists', PlaylistManager::class)->name('playlists');
    Route::get('/dashboard', ArtistDashboard::class)->name('dashboard');
});

