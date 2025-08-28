<?php

namespace App\Filament\Pages\Monitors;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class CTMonitor extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.c-t-monitor';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
