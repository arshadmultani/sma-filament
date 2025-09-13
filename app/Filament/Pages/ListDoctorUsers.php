<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ListDoctorUsers extends Page
{
    // protected static ?string $navigationIcon = 'healthicons-o-health-alt';

    protected static string $view = 'filament.pages.list-doctor-users';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'Dr. Users';

    protected static ?string $title = 'Dr. Users';

    protected static ?int $navigationSort = 3;



    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_user');

    }
}
