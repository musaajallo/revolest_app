<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\FinancialOverviewWidget;
use App\Filament\Widgets\PlatformGrowthChart;
use App\Filament\Widgets\PropertyDistributionChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\LeaseStatusChart;
use App\Filament\Widgets\CircularStatsWidget;
use App\Filament\Widgets\RecentInquiriesWidget;
use App\Filament\Widgets\UpcomingLeaseExpirations;
use App\Filament\Widgets\RecentPaymentsTable;
use App\Filament\Widgets\AlertsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Dashboard';

    protected static ?int $navigationSort = -2;

    protected static ?string $title = 'Main Dashboard';

    protected static ?string $navigationLabel = 'Main Dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            FinancialOverviewWidget::class,
            PlatformGrowthChart::class,
            PropertyDistributionChart::class,
            RevenueChart::class,
            LeaseStatusChart::class,
            CircularStatsWidget::class,
            UpcomingLeaseExpirations::class,
            RecentPaymentsTable::class,
            AlertsWidget::class,
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

        return $user->role === 'super_admin';
    }
}
