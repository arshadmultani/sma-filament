<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MicrositeController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/dr/{slug}', [MicrositeController::class, 'show'])->name('microsite.show');

Route::get('/offline', function () {
    return view('offline');
});
