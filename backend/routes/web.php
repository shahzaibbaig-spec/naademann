<?php

use App\Http\Controllers\Web\Admin\ArtistController as AdminArtistController;
use App\Http\Controllers\Web\Admin\BannerController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\GenreController;
use App\Http\Controllers\Web\Admin\PlatformSettingController;
use App\Http\Controllers\Web\Admin\TrackModerationController;
use App\Http\Controllers\Web\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Ajax\InteractionController;
use App\Http\Controllers\Web\Ajax\SearchController as AjaxSearchController;
use App\Http\Controllers\Web\ArtistPageController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\Creator\DashboardController as CreatorDashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ListenController;
use App\Http\Controllers\Web\PublicStorageController;
use App\Http\Controllers\Web\SearchPageController;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', PublicStorageController::class)
    ->where('path', '.*')
    ->name('storage.public');

Route::get('/', HomeController::class)->name('home');
Route::get('/listen', ListenController::class)->name('listen');
Route::get('/search', SearchPageController::class)->name('search.index');
Route::get('/artists', [ArtistPageController::class, 'index'])->name('artists.index');
Route::get('/artists/{artist}', [ArtistPageController::class, 'show'])->name('artists.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('ajax')->name('ajax.')->group(function (): void {
    Route::get('/search', AjaxSearchController::class)->name('search');

    Route::middleware('auth')->group(function (): void {
        Route::get('/playlists', [InteractionController::class, 'playlists'])->name('playlists');
        Route::post('/songs/{song}/favorite', [InteractionController::class, 'toggleFavorite'])->name('songs.favorite');
        Route::post('/artists/{artist}/follow', [InteractionController::class, 'toggleFollow'])->name('artists.follow');
        Route::post('/songs/{song}/playlist', [InteractionController::class, 'addToPlaylist'])->name('songs.playlist');
    });
});

Route::prefix('creator')
    ->name('creator.')
    ->middleware(['auth', 'role:creator'])
    ->group(function (): void {
        Route::get('/dashboard', [CreatorDashboardController::class, 'index'])->name('dashboard');
        Route::post('/tracks', [CreatorDashboardController::class, 'storeTrack'])->name('tracks.store');
        Route::delete('/tracks/{song}', [CreatorDashboardController::class, 'destroyTrack'])->name('tracks.destroy');
        Route::post('/albums', [CreatorDashboardController::class, 'storeAlbum'])->name('albums.store');
        Route::delete('/albums/{album}', [CreatorDashboardController::class, 'destroyAlbum'])->name('albums.destroy');
        Route::put('/profile', [CreatorDashboardController::class, 'updateProfile'])->name('profile.update');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/artists', [AdminArtistController::class, 'index'])->name('artists.index');
        Route::post('/artists', [AdminArtistController::class, 'store'])->name('artists.store');
        Route::put('/artists/{artist}', [AdminArtistController::class, 'update'])->name('artists.update');
        Route::delete('/artists/{artist}', [AdminArtistController::class, 'destroy'])->name('artists.destroy');
        Route::get('/uploads', [AdminUploadController::class, 'index'])->name('uploads.index');
        Route::post('/uploads', [AdminUploadController::class, 'store'])->name('uploads.store');
        Route::get('/moderation', [TrackModerationController::class, 'index'])->name('moderation.index');
        Route::post('/moderation/bulk', [TrackModerationController::class, 'bulk'])->name('moderation.bulk');
        Route::put('/moderation/{song}', [TrackModerationController::class, 'update'])->name('moderation.update');
        Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
        Route::post('/genres', [GenreController::class, 'store'])->name('genres.store');
        Route::put('/genres/{genre}', [GenreController::class, 'update'])->name('genres.update');
        Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])->name('genres.destroy');
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
        Route::get('/settings', [PlatformSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [PlatformSettingController::class, 'update'])->name('settings.update');
    });
