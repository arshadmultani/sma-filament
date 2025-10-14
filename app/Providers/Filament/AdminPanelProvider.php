<?php

namespace App\Providers\Filament;

use Agencetwogether\HooksHelper\HooksHelperPlugin;
use App\Filament\Auth\CustomLogin;
use App\Http\Middleware\RedirectToProperPanelMiddleware;
use Asmit\ResizedColumn\ResizedColumnPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use Saade\FilamentLaravelLog\FilamentLaravelLogPlugin;


class AdminPanelProvider extends PanelProvider
{
    /**
     * Manually specify the Filament Page classes to show as tabs in the header.
     * Add/remove page classes here as needed.
     */
    protected array $tabPages = [
        \App\Filament\Pages\Dashboard::class,
        \App\Filament\Pages\Customers::class,
        \App\Filament\Pages\CampaignPage::class,
        // Add more custom pages here
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->passwordReset()
            ->profile(isSimple: false)
            ->brandName('StepUp')
            ->favicon(asset('images/icons/icon.png'))
            // ->sidebarCollapsibleOnDesktop()
            // ->brandLogo(fn () => view('filament.admin.logo'))

            ->colors([
                'primary' => Color::Emerald,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit profile'),
                // ...
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Users')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make()
                    ->label('Customers')
                    ->icon('heroicon-o-currency-dollar'),
                NavigationGroup::make()
                    ->label('Activities')
                    ->icon('heroicon-o-chart-bar'),
                NavigationGroup::make()
                    ->label('System'),
                // ->icon('heroicon-o-cog'),
                NavigationGroup::make()
                    ->label('Settings'),
                // ->icon('heroicon-o-cog'),




                NavigationGroup::make()
                    ->label('Dr. Attributes')
                    ->icon('healthicons-o-doctor'),
                NavigationGroup::make()
                    ->label('Territory')
                    ->icon('heroicon-o-map-pin'),


            ])
            ->sidebarWidth('15rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([

            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->breadcrumbs(false)
            ->plugins([
                // FilamentLaravelLogPlugin::make()
                // ->navigationGroup('System'),
                // \BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin::make()
                // ->navigationIcon('heroicon-o-bug-ant')
                // ->navigationGroup('System'),
                FilamentShieldPlugin::make()->gridColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3,
                ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

                FilamentSpatieLaravelHealthPlugin::make()->authorize(function (): bool {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();

                    return $user !== null && $user->hasRole('super_admin');
                }),
                ResizedColumnPlugin::make(),
                // ->preserveOnDB(true),
                // HooksHelperPlugin::make(),

                \RickDBCN\FilamentEmail\FilamentEmail::make(),
                ActivitylogPlugin::make()
                    ->navigationItem(false)

            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->databaseNotifications()

            ->authMiddleware([
                RedirectToProperPanelMiddleware::class,
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        // FilamentView::registerRenderHook(
        //     'panels::topbar.after',
        //     function (): string {
        //         if (request()->routeIs('filament.admin.auth.*')) {
        //             return '';
        //         }
        //         // Use manually specified pages for tabs
        //         $pages = $this->tabPages;
        //         $tabs = [];
        //         foreach ($pages as $pageClass) {
        //             if (!class_exists($pageClass) || !is_subclass_of($pageClass, \Filament\Pages\Page::class)) {
        //                 continue;
        //             }
        //             $tabs[] = [
        //                 'label' => method_exists($pageClass, 'getNavigationLabel') ? $pageClass::getNavigationLabel() : ($pageClass::$navigationLabel ?? $pageClass::$title ?? class_basename($pageClass)),
        //                 'icon' => $pageClass::$navigationIcon ?? 'heroicon-o-document-text',
        //                 'url' => $pageClass::getUrl(),
        //                 'active' => request()->routeIs('filament.admin.pages.' . \Illuminate\Support\Str::kebab(class_basename($pageClass))),
        //             ];
        //         }

        //         return Blade::render(
        //             'components.filament-header-tabs',
        //             ['tabs' => $tabs]
        //         );
        //     }
        // );
        // MOBILE BOTTOM NAV HOOK
        FilamentView::registerRenderHook(
            'panels::body.end',
            function (): string {
                // Show the mobile nav on all Filament admin pages except auth pages
                if (!request()->routeIs('filament.admin.auth.*') && !auth()->user()->hasRole('doctor')) {
                    return Blade::render('filament.admin.mobile-bottom-nav');
                }

                return '';
            }
        );
        // PWA HEAD HOOK
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn(): string => Blade::render('pwa-head')
        );
    }
}
