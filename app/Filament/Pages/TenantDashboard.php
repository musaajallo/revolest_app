<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TenantStatsWidget;
use App\Filament\Widgets\TenantPaymentHistoryWidget;
use App\Filament\Widgets\TenantRepairRequestsWidget;
use App\Filament\Widgets\TenantComplaintsWidget;
use Filament\Pages\Page;

class TenantDashboard extends Page
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $slug = 'tenant-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Tenant Dashboard';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            TenantStatsWidget::class,
            TenantPaymentHistoryWidget::class,
            TenantRepairRequestsWidget::class,
            TenantComplaintsWidget::class,
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

        // Only allow tenants to access this dashboard
        return $user->role === 'tenant';
    }
}
