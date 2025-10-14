<?php

namespace App\Filament\Widgets;

use App\Models\POB;
use App\Models\Microsite;
use App\Models\KofolEntry;
use App\Models\ManagerLogEntry;
use Illuminate\Support\Facades\Gate;
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
        $stats = [];

        if (Gate::allows('viewAny', ManagerLogEntry::class)) {
            $stats[] = Stat::make('Conversion Track', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(ManagerLogEntryResource::getUrl('monitor'));
        }

        if (Gate::allows('viewAny', Microsite::class)) {
            $stats[] = Stat::make('Doctor Website', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(MicrositeResource::getUrl('monitor'));
        }

        if (Gate::allows('viewAny', POB::class)) {
            $stats[] = Stat::make('POB', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(POBResource::getUrl('monitor'));
        }

        if (Gate::allows('viewAny', KofolEntry::class)) {
            $stats[] = Stat::make('KSV', '')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(KofolEntryResource::getUrl('monitor'));
        }
        return $stats;
    }
}
