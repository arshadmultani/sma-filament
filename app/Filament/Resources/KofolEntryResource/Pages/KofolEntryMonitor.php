<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\KofolEntryResource;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolCoupon;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolEntryBooking;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolProductChart;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolProductTable;
use App\Filament\Resources\KofolEntryResource\Widgets\KofolEntryOverview;

class KofolEntryMonitor extends Page
{
    protected static string $resource = KofolEntryResource::class;

    protected static string $view = 'filament.resources.kofol-entry-resource.pages.kofol-entry-monitor';

    public function getTitle(): string
    {
        return 'KSV';
    }
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('View')
                ->icon('heroicon-o-eye')
                ->outlined()
                ->url($this->getResource()::getUrl('index')),
            CreateAction::make()
                ->label('New')
                ->icon('heroicon-o-plus')
                ->url($this->getResource()::getUrl('create')),

        ];

    }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::ScreenTwoExtraLarge;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KofolEntryOverview::class,
            KofolCoupon::class,
            KofolProductTable::class,
            KofolProductChart::class,
            KofolEntryBooking::class,

        ];
    }
}
