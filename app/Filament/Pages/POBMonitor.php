<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\POBResource\Widgets\POBStats;
use App\Filament\Resources\POBResource\Widgets\POBTable;

class POBMonitor extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.p-o-b-monitor';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $navigationGroup = 'System';

    public function getTitle(): string
    {
        return 'POB';
    }

    protected function getHeaderWidgets(): array
    {
        $widgets = [];
        $widgets[] = POBStats::class;

        if (Auth::user()->can('view_user')) {
            $widgets[] = POBTable::class;
        }

        return $widgets;
    }
}
