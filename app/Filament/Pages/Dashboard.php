<?php

namespace App\Filament\Pages;

use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\UserOverview;
use App\Filament\Widgets\CampaignOverview;
use App\Filament\Widgets\CustomerOverview;
class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return 'Hello ' . Auth::user()->name;
    }

    public function getHeaderWidgets(): array{
        $widgets = [];

        if (Auth::user()->hasRole(['super_admin', 'admin'])){
            $widgets[] = UserOverview::class;
        }
        $widgets[]=CampaignOverview::class;
        $widgets[]=CustomerOverview::class;

        return $widgets;
    }
    
}