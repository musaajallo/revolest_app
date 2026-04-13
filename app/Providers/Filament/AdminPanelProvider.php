<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Revolest')
            ->login(Login::class)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('18rem')
            ->collapsedSidebarWidth('4.5rem')
            ->pages([
                Dashboard::class,
            ])
            ->globalSearch(true)
            ->globalSearchKeyBindings(['ctrl+k', 'cmd+k'])
            ->darkMode(true)
            ->colors([
                'primary' => [
                    50 => '240, 253, 246',
                    100 => '220, 252, 233',
                    200 => '187, 247, 212',
                    300 => '134, 239, 180',
                    400 => '74, 222, 139',
                    500 => '34, 197, 102',
                    600 => '28, 71, 54',
                    700 => '21, 61, 43',
                    800 => '15, 46, 33',
                    900 => '10, 31, 22',
                    950 => '5, 16, 11',
                ],
                'danger' => [
                    50 => '254, 246, 243',
                    100 => '253, 232, 224',
                    200 => '251, 207, 192',
                    300 => '247, 171, 145',
                    400 => '241, 125, 86',
                    500 => '169, 74, 42',
                    600 => '138, 60, 34',
                    700 => '107, 46, 26',
                    800 => '77, 33, 19',
                    900 => '47, 20, 12',
                    950 => '26, 11, 6',
                ],
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Visit Website')
                    ->url('/')
                    ->icon('heroicon-o-globe-alt')
                    ->openUrlInNewTab(),
            ])
            ->renderHook(
                'panels::head.end',
                fn () => <<<'HTML'
                <style>
                    @media (min-width: 1024px) {
                        .fi-topbar .fi-global-search-field {
                            width: 32rem !important;
                            max-width: 32rem !important;
                        }
                        .fi-sidebar {
                            resize: horizontal;
                            overflow: auto;
                            min-width: 4.5rem;
                            max-width: 32rem;
                        }
                    }
                    @media (max-width: 1024px) {
                        .fi-topbar .fi-global-search-field {
                            width: min(72vw, 26rem) !important;
                            max-width: 26rem !important;
                        }
                    }
                </style>
                HTML
            )
            ->renderHook(
                'panels::user-menu.before',
                fn () => Blade::render('@livewire(\'inquiry-notifications\')')
            )
            ->navigationGroups([
                'Dashboard',
                'Management',
                'Communication',
                'Properties',
                'CMS',
                'System Management',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
