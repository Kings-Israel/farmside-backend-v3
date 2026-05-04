<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\WebContentController;
use App\Http\Controllers\WebMediaController;
use App\Models\Booking;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'confirmedBookings' => Booking::query()
            ->whereNotNull('confirmed_at')
            ->orderBy('event_date')
            ->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/web-content', [WebContentController::class, 'index'])->name('web-content.index');
Route::post('/web-content', [WebContentController::class, 'update'])->name('web-content.update');
Route::get('/web-media', [WebMediaController::class, 'index'])->name('web-media.index');
Route::post('/web-media/add', [WebMediaController::class, 'store'])->name('web-media.store');
Route::post('/web-media/update', [WebMediaController::class, 'update'])->name('web-media.update');
Route::get('/bookings', [BookingController::class, 'index'])->middleware(['auth'])->name('bookings.index');
Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->middleware(['auth'])->name('bookings.confirm');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
