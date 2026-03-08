<?php

use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\SongController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::get('songs', [SongController::class, 'index']);
Route::get('songs/genres', [SongController::class, 'genres']);
Route::get('songs/{song:slug}', [SongController::class, 'show']);
Route::post('songs/{song:slug}/stream', [SongController::class, 'stream']);

Route::get('artists', [ArtistController::class, 'index']);
Route::get('artists/{artist:slug}', [ArtistController::class, 'show']);

Route::get('playlists', [PlaylistController::class, 'index']);
Route::post('playlists', [PlaylistController::class, 'store']);
Route::get('playlists/{playlist:slug}', [PlaylistController::class, 'show']);
