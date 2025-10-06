<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\POBResource;
use App\Filament\Resources\MicrositeResource;
use App\Filament\Resources\KofolEntryResource;
use App\Filament\Resources\ManagerLogEntryResource;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;

class ActivityOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Conversion Track', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(ManagerLogEntryResource::getUrl('monitor')),
            Stat::make('Doctor Website', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(MicrositeResource::getUrl('monitor')),
            Stat::make('POB', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(POBResource::getUrl('monitor')),
            Stat::make('KSV', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(KofolEntryResource::getUrl('monitor')),

        ];
    }
}
