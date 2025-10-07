<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\MicrositeResource;
use App\Filament\Resources\MicrositeResource\Widgets\MicrositeOverview;

class MicrositeMonitor extends Page
{
    protected static string $resource = MicrositeResource::class;

    protected static string $view = 'filament.resources.microsite-resource.pages.microsite-monitor';

    public function getTitle(): string
    {
        return 'Doctor Website';
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
            MicrositeOverview::class,
        ];
    }
}
