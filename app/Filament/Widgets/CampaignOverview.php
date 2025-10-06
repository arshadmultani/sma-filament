<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\KofolEntry;
use App\Models\KofolEntryCoupon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use App\Notifications\KofolCouponNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CampaignOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [
            Stat::make('Active Campaigns', Campaign::where('end_date', '>=', Carbon::today())->count())
                ->icon('heroicon-s-bolt')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(route('filament.admin.resources.campaigns.index')),
        ];

        return $stats;
    }
}
