<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use App\Models\Doctor;
use App\Models\Chemist;

class CustomerWidget extends BaseWidget
{
    protected int|string|array $columnSpan = [
        'default' => 6, // 1 per row on xs
        'sm' => 3,      // 2 per row on sm and up
    ];
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Doctor::count() + Chemist::count())
                ->color('success')
                ->icon('healthicons-f-circle-medium'),

            // Stat::make('Chemists', Chemist::count())
            //     ->color('success')

            //     ->extraAttributes([
            //         'class' => '',
            //     ])
            //     ->icon('healthicons-f-circle-medium'),

            // Stat::make('Doctors', Doctor::count())
            //     ->color('success')
            //     ->icon('healthicons-f-circle-medium'),

        ];
    }
}
