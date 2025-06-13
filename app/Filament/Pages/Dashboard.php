<?php

namespace App\Filament\Pages;

use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return 'Hello ' . Auth::user()->name;
    }
    
}