<?php

namespace App\Filament\Pages;

use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\CustomerWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return 'Hello ' . Auth::user()->name;
    }
    public function getHeaderWidgets(): array{
        return [
            // CustomerWidget::class,
            // CampaignWidget::class,
            // LatestCustomers::class,
        ];
    }
    
}