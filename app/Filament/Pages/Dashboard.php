<?php

namespace App\Filament\Pages;

use App\Models\Campaign;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
    public function getTitle(): string
    {
        return '';
    }
    public static function getActiveCampaigns(): Collection
    {
        return Campaign::getActiveCampaigns();
    }
}
