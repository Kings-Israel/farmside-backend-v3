<?php

use App\Http\Controllers\WebContentController;
use App\Http\Controllers\WebMediaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/web-content', [WebContentController::class, 'index'])->name('web-content.index');
Route::post('/web-content', [WebContentController::class, 'update'])->name('web-content.update');
Route::get('/web-media', [WebMediaController::class, 'index'])->name('web-media.index');
Route::post('/web-media/add', [WebMediaController::class, 'store'])->name('web-media.store');
Route::post('/web-media/update', [WebMediaController::class, 'update'])->name('web-media.update');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
