<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OwnerStatsWidget;
use App\Filament\Widgets\OwnerPropertiesWidget;
use App\Filament\Widgets\OwnerRecentPaymentsWidget;
use Filament\Pages\Page;

class OwnerDashboard extends Page
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $slug = 'owner-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $title = 'Owner Dashboard';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            OwnerStatsWidget::class,
            OwnerPropertiesWidget::class,
            OwnerRecentPaymentsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return false;
        }

        // Only allow owners to access this dashboard
        return $user->role === 'owner';
    }
}
