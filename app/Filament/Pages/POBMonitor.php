<?php

namespace App\Filament\Pages;

use App\Filament\Resources\POBResource\Widgets\POBStats;
use App\Filament\Resources\POBResource\Widgets\POBTable;
use Filament\Pages\Page;

class POBMonitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.p-o-b-monitor';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected static ?string $navigationGroup = 'System';

    public function getTitle(): string
    {
        return 'POB';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            POBStats::class,
            POBTable::class

        ];
    }
}
