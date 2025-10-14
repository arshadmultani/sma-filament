<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Widgets;

use App\Models\ManagerLogEntry;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class ManagerLogEntryOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tracks', ManagerLogEntry::count())
                ->icon('heroicon-o-bookmark-square'),
        ];
    }
}
