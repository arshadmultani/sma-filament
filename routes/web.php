<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('filament.admin.pages.dashboard');
    }
    return view('welcome');
});

Route::get('/offline', function () {
    return view('offline');
});

