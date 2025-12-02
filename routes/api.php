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
Route::get('/music', [MusicController::class, 'index']);
Route::get('/music/{music}', [MusicController::class, 'show']);
Route::post('/music', [MusicController::class, 'store'])->middleware('auth:sanctum');
Route::put('/music/{music}', [MusicController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/music/{music}', [MusicController::class, 'destroy'])->middleware('auth:sanctum');
