<?php


use App\Http\Controllers\AutoShortController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReelScraperController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;



Route::get('/create-video', [VideoController::class, 'form'])->name('video.form');
Route::post('/create-video', [VideoController::class, 'generate'])->name('video.generate');


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/auto-shorts', [AutoShortController::class, 'form'])->name('auto.form');
Route::post('/auto-shorts', [AutoShortController::class, 'generate'])->name('auto.generate');



Route::get('/reels-scraper', [ReelScraperController::class, 'form'])->name('reels.form');
Route::post('/reels-scraper', [ReelScraperController::class, 'search'])->name('reels.search');
Route::get('/reels/download', [ReelScraperController::class, 'download'])->name('reels.download');



