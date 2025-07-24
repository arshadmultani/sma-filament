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
        $totalSent = KofolEntry::whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('notifications')
                ->whereRaw('notifications.notifiable_id = kofol_entries.customer_id')
                ->whereRaw('notifications.notifiable_type = kofol_entries.customer_type')
                ->where('notifications.type', KofolCouponNotification::class)
                ->whereRaw("(notifications.data->>'kofol_entry_id')::int = kofol_entries.id");
        })->count();

        $sentToday = KofolEntry::whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('notifications')
                ->whereRaw('notifications.notifiable_id = kofol_entries.customer_id')
                ->whereRaw('notifications.notifiable_type = kofol_entries.customer_type')
                ->where('notifications.type', KofolCouponNotification::class)
                ->whereRaw("(notifications.data->>'kofol_entry_id')::int = kofol_entries.id")
                ->whereDate('notifications.created_at', Carbon::today());
        })->count();

        $stats = [
            Stat::make('Active Campaigns', Campaign::where('is_active', true)->count())
                ->icon('heroicon-s-bolt')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
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
            Stat::make(
                'Coupons Generated',
                KofolEntryCoupon::whereIn(
                    'kofol_entry_id',
                    KofolEntry::query()->pluck('id')
                )->count()
            )
                ->icon('heroicon-s-ticket')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary'),

            Stat::make('Approved Invoices Amount', (new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY))->formatCurrency(KofolEntry::where('status', 'Approved')->sum('invoice_amount'), 'INR'))

                ->icon('heroicon-s-currency-rupee')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary'),
        ];

        // Conditionally add the Emails Sent stat
        if (Auth::user()?->can('view_user')) {
            $stats[] = Stat::make('Emails Sent', $totalSent)
                ->icon('heroicon-s-envelope')
                ->iconColor('primary')
                ->textColor('primary', '', descriptionColor: 'primary')
                // ->description("Today: {$sentToday}")
                ->descriptionColor('success');
        }

        return $stats;
    }
}
