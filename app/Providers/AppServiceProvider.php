<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Commands;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Commands\SetupCommand::prohibit($this->app->environment('local'));
        // Commands\InstallCommand::prohibit($this->app->environment('local'));
        // Commands\GenerateCommand::prohibit($this->app->environment('local'));
        // Commands\PublishCommand::prohibit($this->app->environment('local'));

        FilamentShield::prohibitDestructiveCommands($this->app->environment('production'));

    }
}
