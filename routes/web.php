<?php

use App\Http\Controllers\ArController;
use App\Http\Controllers\MicrositeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// TODO: consider separate domain for doctor microsites
// Route::domain('doctor.test')->group(function () {
//     Route::get('/dr/{slug}', [MicrositeController::class, 'show'])->name('microsite.show');
// });

Route::get('/dr/{slug}', [MicrositeController::class, 'show'])->name('microsite.show');

Route::get('/offline', function () {
    return view('offline');
});

// Public augmented-reality viewer + admin-only browser-compile endpoint.
Route::get('/ar/{creative}', [ArController::class, 'show'])->name('ar.show');
Route::middleware('auth')
    ->post('/ar/{creative}/compile', [ArController::class, 'storeMind'])
    ->name('ar.compile');
