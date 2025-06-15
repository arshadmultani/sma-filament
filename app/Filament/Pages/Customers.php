<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CustomerOverviewWidget;
use App\Filament\Widgets\CustomerWidget;
use App\Filament\Widgets\CampaignWidget;
use App\Filament\Widgets\LatestCustomers;

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
    protected function getHeaderWidgets(): array{
        return [
            // CustomerWidget::class,
            // CampaignWidget::class,
            // LatestCustomers::class,
        ];
    }
}
