<?php

namespace App\Filament\Resources\MicrositeResource\Widgets;

use App\Models\Microsite;
use App\Filament\Resources\MicrositeResource;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;

class MicrositeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Websites', Microsite::count())
                ->icon('heroicon-s-globe-alt')
                ->url(MicrositeResource::getUrl('index')),
        ];
    }
}
