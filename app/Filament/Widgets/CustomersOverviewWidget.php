<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use App\Models\Doctor;
use App\Models\Chemist;

class CustomersOverviewWidget extends BaseWidget
{
    protected ?string $heading = 'Customers';
    protected ?string $description = 'An overview of some analytics.';


    public function goToDoctors()
    {
        return redirect()->route('filament.admin.resources.doctors.index');
    }
    public function goToChemists()
    {
        return redirect()->route('filament.admin.resources.chemists.index');
    }
    protected function getHeading(): ?string
    {
        return 'Customers';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Doctors', Doctor::count())
                ->icon('healthicons-f-stethoscope')
                ->backgroundColor('amber')
                ->chartColor('success')
                ->iconPosition('start')
                ->iconColor('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "goToDoctors",
                ]),
            Stat::make('Chemists', Chemist::count())
                ->icon('healthicons-f-pharmacy')
                ->backgroundColor('amber')
                ->textColor('', 'success', 'info')
                ->iconPosition('start')
                ->descriptionColor('success')
                ->iconColor('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "goToChemists",
                ]),
        ];
    }
}
