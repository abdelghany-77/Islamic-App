<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\HadithController;

Route::get('/', function () { return view('welcome');});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Quran Routes
    Route::get('/quran', [QuranController::class, 'index'])->name('quran.index');
    Route::get('/quran/{surah}/{page?}', [QuranController::class, 'show'])->name('quran.show');
    Route::get('/quran/search', [QuranController::class, 'search'])->name('quran.search');
    // Hadith Routes
    Route::get('/hadith', [HadithController::class, 'index'])->name('hadith.index');
    Route::get('/hadith/book/{book}', [HadithController::class, 'showBook'])->name('hadith.book');
    Route::get('/hadith/{hadith}', [HadithController::class, 'show'])->name('hadith.show');
    Route::get('/hadith/search', [HadithController::class, 'search'])->name('hadith.search');


