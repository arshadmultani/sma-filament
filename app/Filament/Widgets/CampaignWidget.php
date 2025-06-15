<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use App\Models\KofolCampaign;
use App\Models\KofolEntry;

class CampaignWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Campaigns', KofolCampaign::where('is_active', true)->count())
            ->icon('healthicons-f-circle-medium')
            ->iconColor('success')
            // ->description('Total number of campaigns')
            // ->descriptionIcon('heroicon-o-chart-bar')
            ->color('success'),
    //    Stat::make('Campaign Entries', KofolEntry::count()),
    //    Stat::make('Coupons Generated', KofolEntry::where('coupon_code', '!=', null)->count()),
    
        ];
    }
}
