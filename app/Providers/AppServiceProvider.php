<?php

namespace App\Providers;

use App\Models\User;
use BezhanSalleh\FilamentShield\Commands;
use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Facades\Health;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\KofolEntry;
use App\Models\Microsite;
use App\Models\Doctor;
use App\Models\Chemist;
use App\Models\Headquarter;
use App\Models\Area;
use App\Models\Region;
use App\Models\Zone;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


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
        $this->configureCommands();
        $this->configureUrls();
        $this->configureModels();
        $this->configureDates();



        // Storage::disk('s3')->buildTemporaryUrlsUsing(fn ($path) =>
        //     Storage::disk('s3')->temporaryUrl($path, now()->addDays(7))
        // );
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
            'microsite' => Microsite::class,
            'doctor' => Doctor::class,
            'chemist' => Chemist::class,
            'headquarter' => Headquarter::class,
            'area' => Area::class,
            'region' => Region::class,
            'zone' => Zone::class,
        ]);

        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });

        //Sends filament validation errors to the user 
        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
    }

    private function configureCommands(): void
    {/** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        DB::prohibitDestructiveCommands($app->isProduction());
        FilamentShield::prohibitDestructiveCommands($app->isProduction());
    }
    private function configureUrls(): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        if ($app->isProduction()) {
            URL::forceScheme('https');
        }
    }
    private function configureModels(): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        Model::shouldBeStrict(!$app->isProduction());
        Model::unguard();
    }

    /**
     * Configure the dates.
     */
    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
