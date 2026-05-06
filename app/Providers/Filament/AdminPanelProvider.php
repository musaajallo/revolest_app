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
            ->favicon(function () {
                $favicon = \App\Models\Setting::get('site_favicon');

                return $favicon
                    ? \Illuminate\Support\Facades\Storage::url($favicon)
                    : asset('favicon.svg');
            })
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
                        /* Center the global search in the topbar.
                           The default Filament v3 topbar wraps search + notifications +
                           user-menu in a single .ms-auto right-aligned cluster. We pull
                           the search out of flow and absolute-center it; notifications,
                           Visit Site link, and user-menu stay on the right. */
                        .fi-topbar > nav {
                            position: relative;
                        }
                        .fi-topbar .fi-global-search {
                            position: absolute;
                            left: 50%;
                            transform: translateX(-50%);
                        }
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
                    /* Visit Site link in topbar */
                    .fi-topbar-visit-site {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.375rem;
                        padding: 0.375rem 0.75rem;
                        font-size: 0.875rem;
                        font-weight: 500;
                        line-height: 1.25rem;
                        color: rgb(75 85 99);
                        border-radius: 0.5rem;
                        transition: background-color .15s, color .15s;
                    }
                    .fi-topbar-visit-site:hover {
                        background-color: rgb(243 244 246);
                        color: rgb(28 71 54);
                    }
                    .dark .fi-topbar-visit-site {
                        color: rgb(209 213 219);
                    }
                    .dark .fi-topbar-visit-site:hover {
                        background-color: rgb(255 255 255 / 0.05);
                        color: rgb(74 222 139);
                    }
                </style>
                HTML
            )
            ->renderHook(
                'panels::user-menu.before',
                fn () => Blade::render(<<<'BLADE'
                <a
                    href="{{ url('/') }}"
                    target="_blank"
                    rel="noopener"
                    class="fi-topbar-visit-site"
                    title="Open the public website in a new tab"
                >
                    <x-filament::icon
                        icon="heroicon-o-arrow-top-right-on-square"
                        class="h-4 w-4"
                    />
                    <span class="hidden sm:inline">Visit Site</span>
                </a>
                @livewire('inquiry-notifications')
                BLADE)
            )
            ->navigationGroups([
                'Dashboard',
                'Management',
                'Communication',
                'Submissions',
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
