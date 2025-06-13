<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CustomerOverviewWidget;

class Campaigns extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.campaigns';

    protected function getHeaderWidgets(): array{
        return [
            CustomerOverviewWidget::class,
        ];
    }
}
