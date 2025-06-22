<?php

namespace App\Providers;

use App\Models\User;
use BezhanSalleh\FilamentShield\Commands;
use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Facades\Health;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\KofolEntry;
use App\Models\Microsite;
use App\Models\Doctor;
use App\Models\Chemist;

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

        Health::checks([
            OptimizedAppCheck::new(),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
        ]);
        Gate::define('viewPulse', function (User $user) {
            return $user->email == 'arshadrmultani@gmail.com';
        });
        Relation::morphMap([
            'kofol_entry' => KofolEntry::class,
            'microsite_entry' => Microsite::class,
            'doctor' => Doctor::class,
            'chemist' => Chemist::class,
        ]);
    }
}
