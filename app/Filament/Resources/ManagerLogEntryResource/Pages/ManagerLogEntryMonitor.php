<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource\Widgets\ManagerLogEntryOverview;
use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ManagerLogEntryResource;

class ManagerLogEntryMonitor extends Page
{
    protected static string $resource = ManagerLogEntryResource::class;

    protected static string $view = 'filament.resources.manager-log-entry-resource.pages.manager-log-entry-monitor';

    public function getTitle(): string
    {
        return 'Conversion Track';
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

    protected function getHeaderWidgets(): array
    {
        return [
            ManagerLogEntryOverview::class,
        ];
    }
}
