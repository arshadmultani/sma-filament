<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Filament\Resources\Pages\Page;

class KofolEntryMonitor extends Page
{
    protected static string $resource = KofolEntryResource::class;

    protected static string $view = 'filament.resources.kofol-entry-resource.pages.kofol-entry-monitor';
}
