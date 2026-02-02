<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JamController;
use App\Http\Controllers\JamQueueController;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     // return view('welcome');
//     return view('index');
// })->name('index');

Route::get('/', [DashboardController::class, 'dashboard'])
    ->middleware(['auth'])
    ->name('dashboard');

// Public jam join route (before auth middleware)
Route::get('/jam/join/{code}', [JamController::class, 'joinPage'])->name('jam.join');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/music/search', [DashboardController::class, 'search'])->name('music.search');
    Route::post('/music/queue', [DashboardController::class, 'queue'])->name('music.queue');

    // Jam routes
    Route::get('/jams', [JamController::class, 'index'])->name('jam.index');
    Route::get('/jam/create', [JamController::class, 'create'])->name('jam.create');
    Route::post('/jam', [JamController::class, 'store'])->name('jam.store');
    Route::get('/jam/{jam}', [JamController::class, 'show'])->name('jam.show');
    Route::get('/jam/{jam}/edit', [JamController::class, 'edit'])->name('jam.edit');
    Route::patch('/jam/{jam}', [JamController::class, 'update'])->name('jam.update');
    Route::delete('/jam/{jam}', [JamController::class, 'destroy'])->name('jam.destroy');
    Route::post('/jam/join/{code}', [JamController::class, 'join'])->name('jam.join.submit');
    Route::post('/jam/{jam}/leave', [JamController::class, 'leave'])->name('jam.leave');
    Route::post('/jam/{jam}/search', [JamController::class, 'search'])->name('jam.search');
    Route::get('/jam/{jam}/artist/{artistId}/top-tracks', [JamController::class, 'artistTopTracks'])->name('jam.artist.top-tracks');

    // Jam Queue routes
    Route::post('/jam/{jam}/queue', [JamQueueController::class, 'store'])->name('jam.queue.store');
    Route::delete('/jam/{jam}/queue/{queueItem}', [JamQueueController::class, 'destroy'])->name('jam.queue.destroy');
    Route::post('/jam/{jam}/queue/reorder', [JamQueueController::class, 'reorder'])->name('jam.queue.reorder');
    Route::post('/jam/{jam}/queue/{queueItem}/playing', [JamQueueController::class, 'markPlaying'])->name('jam.queue.playing');
    Route::post('/jam/{jam}/queue/{queueItem}/push-spotify', [JamQueueController::class, 'pushToSpotify'])->name('jam.queue.push-spotify');
});

require __DIR__.'/auth.php';
