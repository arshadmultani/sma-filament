<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource;
use Filament\Resources\Pages\Page;

class ManagerLogEntryMonitor extends Page
{
    protected static string $resource = ManagerLogEntryResource::class;

    protected static string $view = 'filament.resources.manager-log-entry-resource.pages.manager-log-entry-monitor';
}
