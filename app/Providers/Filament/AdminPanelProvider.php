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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('SÀ Property')
            ->login(Login::class)
            ->colors([
                'primary' => [
                    50 => '254, 242, 242',
                    100 => '254, 226, 226',
                    200 => '254, 202, 202',
                    300 => '252, 165, 165',
                    400 => '248, 113, 113',
                    500 => '212, 19, 19',
                    600 => '185, 17, 17',
                    700 => '153, 14, 14',
                    800 => '127, 12, 12',
                    900 => '105, 10, 10',
                    950 => '69, 7, 7',
                ],
                'success' => [
                    50 => '236, 253, 245',
                    100 => '209, 250, 229',
                    200 => '167, 243, 208',
                    300 => '110, 231, 183',
                    400 => '52, 211, 153',
                    500 => '0, 166, 118',
                    600 => '0, 140, 100',
                    700 => '0, 115, 82',
                    800 => '0, 91, 65',
                    900 => '0, 75, 54',
                    950 => '0, 50, 36',
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
