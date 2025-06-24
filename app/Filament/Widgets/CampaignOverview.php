<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\KofolEntry;
use App\Models\KofolEntryCoupon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class CampaignOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Campaigns', Campaign::where('is_active', true)->count())
                ->backgroundColor('')
                ->icon('heroicon-s-bolt')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->backgroundColor('')
                ->url(route('filament.admin.resources.campaigns.index')),

            Stat::make('Total KSV Bookings', KofolEntry::count())
                ->icon('heroicon-s-document-plus')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(route('filament.admin.resources.kofol-entries.index')),
            Stat::make('Approved Bookings', KofolEntry::where('status', 'Approved')->count())
                ->icon('heroicon-s-check-circle')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
                ->url(route('filament.admin.resources.kofol-entries.index', [
                    'activetab' => 'approved'
                ])),
            Stat::make('Coupons Generated',KofolEntryCoupon::count())
            ->icon('heroicon-s-ticket')
            ->iconColor('primary')
            ->textColor('primary', '', descriptionColor: 'primary'),

            Stat::make('Approved Invoices Amount', '₹' . number_format(KofolEntry::where('status', 'Approved')->sum('invoice_amount')))
                ->icon('heroicon-s-currency-rupee')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
        ];
    }
}
