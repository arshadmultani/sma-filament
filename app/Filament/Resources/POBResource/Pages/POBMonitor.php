<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Filament\Resources\POBResource;
use Filament\Resources\Pages\Page;

class POBMonitor extends Page
{
    protected static string $resource = POBResource::class;

    protected static string $view = 'filament.resources.p-o-b-resource.pages.p-o-b-monitor';
}
