<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CampaignPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.campaign-page';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // CustomerOverviewWidget::class,
        ];
    }
}
