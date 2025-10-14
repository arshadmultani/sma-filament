<?php

namespace App\Filament\Resources\POBResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;

use Filament\Resources\Pages\Page;

use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\POBResource;
use App\Filament\Resources\POBResource\Widgets\POBStats;
use App\Filament\Resources\POBResource\Widgets\POBTable;

class POBMonitor extends Page
{
    protected static string $resource = POBResource::class;

    protected static string $view = 'filament.resources.p-o-b-resource.pages.p-o-b-monitor';

    public function getTitle(): string
    {
        return 'POB';
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
        $widgets = [];
        $widgets[] = POBStats::class;

        if (auth()->user()->can('view_user')) {
            $widgets[] = POBTable::class;
        }
        return $widgets;
    }
}
