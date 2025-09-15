<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/doctor/{slug}', [\App\Http\Controllers\MicrositeController::class, 'show'])->name('microsite.show');

Route::get('/offline', function () {
    return view('offline');
});
