<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CampaignWidget;
use App\Filament\Widgets\CustomerWidget;
use App\Filament\Widgets\LatestCustomers;
use Filament\Pages\Page;

class Customers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected ?string $heading = 'Customers';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    //     public static function getColumns(): int|array
    // {
    //     return [
    //         'default' => 6,
    //         'sm' => 6,
    //     ];
    // }
    protected static string $view = 'filament.pages.customers';

    protected function getHeaderWidgets(): array
    {
        return [
            // CustomerWidget::class,
            // CampaignWidget::class,
            // LatestCustomers::class,
        ];
    }
}
