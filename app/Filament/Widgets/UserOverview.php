<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ListDoctorUsers;
use App\Filament\Resources\UserResource;
use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::whereDoesntHave('roles', function ($query) {
                $query->where('name', ['super_admin', 'admin', 'doctor']);
            })->count()) //TODO: solve the query count problem
                ->icon('heroicon-s-user-group')
                ->url(UserResource::getUrl('index')),
            Stat::make('Doctor Users', User::whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })->count())
                ->icon('healthicons-f-doctor-male')
                ->url(ListDoctorUsers::getUrl()),

        ];
    }
}
