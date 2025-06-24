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
                ->textColor('warning', '', descriptionColor: 'warning')
                ->iconColor('warning')
                ->url(route('filament.admin.pages.customers')),

            Stat::make('Chemists', Chemist::count())
                ->color('success')
                ->iconColor('warning')
                ->icon('healthicons-f-medicine-mortar')
                ->textColor('warning', '', descriptionColor: 'warning')

                ->url(route('filament.admin.resources.chemists.index')),

            Stat::make('Doctors', Doctor::count())
                ->color('success')
                ->iconColor('warning')
                ->icon('healthicons-f-doctor-male')
                ->textColor('warning', '', descriptionColor: 'warning')

                ->url(route('filament.admin.resources.doctors.index')),
        ];
    }
}
