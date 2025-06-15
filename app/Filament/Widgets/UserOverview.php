<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use App\Models\User;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::whereDoesntHave('roles', function($query) {
                $query->where('name', 'super_admin');
            })->count())
            ->icon('heroicon-s-user-group')
            ->backgroundColor('primary'),
            ];
        }
       
}
