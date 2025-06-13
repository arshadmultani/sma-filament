<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CustomerOverviewWidget;

class Customers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.customers';
    protected function getHeaderWidgets(): array{
        return [
            CustomerOverviewWidget::class,
        ];
    }
}
