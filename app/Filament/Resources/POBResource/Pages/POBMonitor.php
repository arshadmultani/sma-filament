<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource\Widgets\POBStats;
use App\Filament\Resources\POBResource\Widgets\POBTable;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\POBResource;

class POBMonitor extends Page
{
    protected static string $resource = POBResource::class;

    protected static string $view = 'filament.resources.p-o-b-resource.pages.p-o-b-monitor';

    public function getTitle(): string
    {
        return 'POB';
    }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::ScreenTwoExtraLarge;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            POBStats::class,
            POBTable::class,
        ];
    }
}
