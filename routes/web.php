<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\AzkarController;
use App\Http\Controllers\DuaaController;

// Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Quran routes
Route::get('/quran', [QuranController::class, 'index'])->name('quran.index');
Route::get('/quran/surah/{surahNumber}', [QuranController::class, 'surah'])->name('quran.surah');
Route::get('/quran/surah/{number}/tafsir', [QuranController::class, 'tafsir'])->name('quran.tafsir');
Route::get('/quran/search', [QuranController::class, 'search'])->name('quran.search');

// Azkar Routes
Route::prefix('azkar')->name('azkar.')->group(function () {
    Route::get('/', [AzkarController::class, 'index'])->name('index');
    Route::get('/azkar/{category}', [AzkarController::class, 'category'])->name('category');
});

// Duaa Routes
Route::get('/duaa', [DuaaController::class, 'index'])->name('duaa.index');
Route::get('/duaa/{slug}', [DuaaController::class, 'category'])->name('duaa.category');
Route::get('/duaa/{slug}/{id}', [DuaaController::class, 'show'])->name('duaa.show');
