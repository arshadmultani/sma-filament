<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CampaignOverview;

class Campaigns extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.campaigns';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    protected function getHeaderWidgets(): array{
        return [
            // CustomerOverviewWidget::class,
        ];
    }
}
