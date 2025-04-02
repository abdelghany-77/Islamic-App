<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\AzkarController;
use App\Http\Controllers\DuaaController;
use App\Http\Controllers\IslamicStoryController;
use App\Http\Controllers\Admin\AdminStoryController;
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

// Dua Routes
Route::get('/duaa', [DuaaController::class, 'index'])->name('duaa.index');
Route::get('/duaa/{slug}', [DuaaController::class, 'category'])->name('duaa.category');
Route::get('/duaa/{slug}/{id}', [DuaaController::class, 'show'])->name('duaa.show');

// Islamic Stories Routes
Route::prefix('stories')->name('stories.')->group(function () {
    Route::get('/', [IslamicStoryController::class, 'index'])->name('index');
    Route::get('/category/{category}', [IslamicStoryController::class, 'category'])->name('category');
    Route::get('/view/{id}', [IslamicStoryController::class, 'show'])->name('show');
    Route::get('/search', [IslamicStoryController::class, 'search'])->name('search');
});
// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/stories', [AdminStoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/create', [AdminStoryController::class, 'create'])->name('stories.create');
    Route::post('/stories', [AdminStoryController::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}/edit', [AdminStoryController::class, 'edit'])->name('stories.edit');
    Route::put('/stories/{story}', [AdminStoryController::class, 'update'])->name('stories.update');
    Route::delete('/stories/{story}', [AdminStoryController::class, 'destroy'])->name('stories.destroy');
    Route::get('/stories/category/{category}', [IslamicStoryController::class, 'category'])->name('stories.category');
    // Route::patch('/stories/{story}/toggle-featured', [AdminStoryController::class, 'toggleFeatured'])->name('admin.stories.toggle-featured');
});
