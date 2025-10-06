<?php

namespace App\Filament\Resources\MicrositeResource\Pages;

use App\Filament\Resources\MicrositeResource\Widgets\MicrositeOverview;
use Filament\Resources\Pages\Page;
use App\Filament\Widgets\CustomerOverview;
use App\Filament\Resources\MicrositeResource;

class MicrositeMonitor extends Page
{
    protected static string $resource = MicrositeResource::class;

    protected static string $view = 'filament.resources.microsite-resource.pages.microsite-monitor';

    public function getTitle(): string
    {
        return 'Doctor Website';
    }
    protected function getHeaderWidgets(): array
    {
        return [
            MicrositeOverview::class,
        ];
    }
}
