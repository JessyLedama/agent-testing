<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User routes
Route::post('/user/become-artist', [UserController::class, 'becomeArtist'])
    ->middleware('auth:sanctum');

// Category routes
Route::apiResource('categories', CategoryController::class);

// Music routes
Route::apiResource('music', MusicController::class);
