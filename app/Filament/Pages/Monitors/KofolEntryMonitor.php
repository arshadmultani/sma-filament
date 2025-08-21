<?php

namespace App\Filament\Pages\Monitors;

use App\Filament\Resources\KofolEntryResource\Widgets\KofolCoupon;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolEntryBooking;
use Filament\Pages\Page;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolProductChart;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolEntryOverview;

class KofolEntryMonitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.kofol-entry-monitor';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $navigationGroup = 'System';


    protected function getHeaderWidgets(): array
    {
        return [
            KofolEntryOverview::class,
            KofolProductChart::class,
            KofolEntryBooking::class,
            KofolCoupon::class

        ];
    }

}
