<?php

namespace App\Filament\Widgets;

use App\Models\Chemist;
use App\Models\Doctor;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class CustomerOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Doctor::count() + Chemist::count())
                ->color('success')
                ->icon('healthicons-f-agriculture')
                ->backgroundColor('')
                ->textColor('info', 'info', descriptionColor: 'info')
                ->iconColor('info'),

            Stat::make('Chemists', Chemist::count())
                ->color('success')
                ->iconColor('info')
                ->iconPosition('start')
                ->backgroundColor('warning')
                ->icon('healthicons-f-medicine-mortar'),

            Stat::make('Doctors', Doctor::count())
                ->color('success')
                ->backgroundColor('warning')
                ->iconPosition('start')
                ->iconColor('info')
                ->icon('healthicons-f-doctor-male'),
        ];
    }
}
