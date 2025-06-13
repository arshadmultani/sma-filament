<?php

namespace App\Providers\Filament;

use App\Filament\Auth\CustomLogin;
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
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use Illuminate\Support\Facades\Auth;
use Agencetwogether\HooksHelper\HooksHelperPlugin;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use App\Filament\Widgets\CustomerOverviewWidget;
use Illuminate\Support\Str;




class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login(CustomLogin::class)
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->passwordReset()
            ->spa()
            ->unsavedChangesAlerts()
            ->profile(isSimple: false)
            ->brandName('Charak SMA')
            ->favicon(asset('public/logo.svg'))
            ->sidebarCollapsibleOnDesktop()
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
                    ->label('Customer')
                    ->icon('heroicon-o-currency-dollar'),
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
                CustomerOverviewWidget::class,
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
                FilamentShieldPlugin::make()->gridColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3
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

                SimpleLightBoxPlugin::make(),
                FilamentSpatieLaravelHealthPlugin::make()->authorize(function (): bool {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();
                    return $user !== null && $user->hasRole('super_admin');
                }),
                HooksHelperPlugin::make(),

                // \RickDBCN\FilamentEmail\FilamentEmail::make(),
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->databaseNotifications()

            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::topbar.after',
            function (): string {
                // Exclude login/auth pages
                if (request()->routeIs('filament.admin.auth.*')) {
                    return '';
                }
                // Gather all custom pages
                $pages = [
                    \App\Filament\Pages\Dashboard::class,
                    ...array_filter(
                        array_map(function ($file) {
                            $class = 'App\\Filament\\Pages\\' . pathinfo($file, PATHINFO_FILENAME);
                            return $class !== 'App\\Filament\\Pages\\Dashboard' ? $class : null;
                        }, glob(app_path('Filament/Pages/*.php')))
                    ),
                ];
                $tabs = [];
                foreach ($pages as $pageClass) {
                    if (!class_exists($pageClass) || !is_subclass_of($pageClass, \Filament\Pages\Page::class)) continue;
                    $tabs[] = [
                        'label' => method_exists($pageClass, 'getNavigationLabel') ? $pageClass::getNavigationLabel() : ($pageClass::$navigationLabel ?? $pageClass::$title ?? class_basename($pageClass)),
                        'icon' => $pageClass::$navigationIcon ?? 'heroicon-o-document-text',
                        'url' => $pageClass::getUrl(),
                        'active' => request()->routeIs('filament.admin.pages.' . \Illuminate\Support\Str::kebab(class_basename($pageClass))),
                    ];
                }
                return \Illuminate\Support\Facades\Blade::render(
                    'components.filament-header-tabs', ['tabs' => $tabs]
                );
            }
        );
        FilamentView::registerRenderHook(
            'panels::body.end',
            function (): string {
                // Only show the mobile nav on dashboard and resource pages, not on auth pages
                if (request()->routeIs('filament.admin.pages.dashboard') || request()->routeIs('filament.admin.resources.*')) {
                    return Blade::render('filament.admin.mobile-bottom-nav');
                }
                return '';
            }
        );
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn(): string => Blade::render('pwa-head')
        );
    }
}
