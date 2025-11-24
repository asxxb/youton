<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutoShortController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReelScraperController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\YouTubeController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {

Route::get('/create-video', [VideoController::class, 'form'])->name('video.form');
Route::post('/create-video', [VideoController::class, 'generate'])->name('video.generate');


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/auto-shorts', [AutoShortController::class, 'form'])->name('auto.form');
Route::post('/auto-shorts', [AutoShortController::class, 'generate'])->name('auto.generate');



Route::get('/reels-scraper', [ReelScraperController::class, 'form'])->name('reels.form');
Route::post('/reels-scraper', [ReelScraperController::class, 'search'])->name('reels.search');
Route::get('/reels/download', [ReelScraperController::class, 'download'])->name('reels.download');




Route::get('/auto/multi', [AutoShortController::class, 'multiForm'])->name('auto.multi.form');
Route::post('/auto/multi', [AutoShortController::class, 'multiGenerate'])->name('auto.multi.generate');


Route::get('/auth/youtube', [YouTubeController::class, 'redirect'])->name('youtube.redirect');
Route::get('/auth/youtube/callback', [YouTubeController::class, 'callback'])->name('youtube.callback');

// Route::post('/upload-to-youtube', [YouTubeController::class, 'upload'])->name('youtube.upload');


Route::post('/upload-to-youtube', [YouTubeController::class, 'uploadResumable'])->name('youtube.upload');


Route::get('/youtube/details', [YouTubeController::class, 'channelInfo'])->name('youtube.details');

Route::get('/ai/platform', [DashboardController::class, 'platform'])->name('ai.platform');


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


 Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');

    // UPDATE SETTINGS
    Route::post('/settings/update', [SettingsController::class, 'update'])
        ->name('settings.update');
});