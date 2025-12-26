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
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\MaxWidth;
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
                fn () => '<style>
                    .fi-global-search {
                        position: absolute !important;
                        left: 50% !important;
                        transform: translateX(-50%) !important;
                        width: 100% !important;
                        max-width: 600px !important;
                    }
                    @media (max-width: 1024px) {
                        .fi-global-search {
                            position: relative !important;
                            left: auto !important;
                            transform: none !important;
                            max-width: 350px !important;
                        }
                    }
                </style>'
            )
            ->renderHook(
                'panels::global-search.before',
                fn () => Blade::render('<a href="/" target="_blank" class="fi-btn fi-btn-size-sm inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold outline-none transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-700 dark:text-gray-200">
                    <x-heroicon-o-globe-alt class="h-5 w-5" />
                    <span>Visit Website</span>
                </a>')
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
