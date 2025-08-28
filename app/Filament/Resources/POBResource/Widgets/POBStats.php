<?php

namespace App\Filament\Resources\POBResource\Widgets;

use App\Models\POB;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class POBStats extends BaseWidget
{
    protected function getStats(): array
    {
        $formatter = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 1);

        return [
            Stat::make('Total POBs', POB::count())
                ->icon('heroicon-o-document-currency-rupee')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary'),
            Stat::make('Approved POBs', POB::approvedCount())
                ->icon('heroicon-o-check-circle')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary'),
            Stat::make('Approved POB Value', $formatter->formatCurrency(POB::approved()->sum('invoice_amount'), 'INR'))
                ->icon('heroicon-o-currency-rupee')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary'),
        ];
    }
}
