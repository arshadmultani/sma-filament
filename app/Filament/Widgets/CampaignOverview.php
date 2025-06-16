<?php

namespace App\Filament\Widgets;

use App\Models\KofolCampaign;
use App\Models\KofolEntry;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class CampaignOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Campaigns', KofolCampaign::where('is_active', true)->count())
                ->backgroundColor('')
                ->icon('heroicon-s-bolt')
                ->iconColor('warning')
                ->textColor('warning', 'warning', descriptionColor: 'warning')
                ->backgroundColor(''),

            Stat::make('Campaign Entries', KofolEntry::count())
                ->icon('heroicon-s-document-plus')
                ->backgroundColor('info')
                ->iconPosition('start')
                ->iconColor('warning'),
            Stat::make('Coupons Generated', KofolEntry::where('coupon_code', '!=', null)->count())
                ->icon('heroicon-s-ticket')
                ->iconPosition('start')
                ->backgroundColor('info')
                ->iconColor('warning'),

        ];
    }
}
