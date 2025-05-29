<?php

use App\Http\Controllers\WebContentController;
use App\Http\Controllers\WebMediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/web-content', [WebContentController::class, 'index'])->name('web-content.index');
Route::get('/web-media', [WebMediaController::class, 'index'])->name('web-media.index');
