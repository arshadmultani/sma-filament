<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use App\Models\Doctor;
use App\Models\Chemist;

class CustomerOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Doctors', Doctor::count())
                ->icon('healthicons-f-stethoscope')
                ->color('success'),
            Stat::make('Chemists', Chemist::count())
                ->icon('healthicons-o-health-alt')
                ->color('success')
        ];
    }
}
