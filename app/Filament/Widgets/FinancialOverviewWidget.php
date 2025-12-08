<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Lease;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Total Revenue
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        // This Month's Revenue
        $thisMonthRevenue = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('amount');

        // Last Month's Revenue
        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        $revenueTrend = $lastMonthRevenue > 0 
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Pending Payments
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $pendingCount = Payment::where('status', 'pending')->count();

        // Active Leases Value
        $activeLeases = Lease::where('status', 'active')->count();
        $monthlyRentTotal = Lease::where('status', 'active')->sum('rent_amount');

        return [
            Stat::make('Total Revenue', 'D ' . number_format($totalRevenue, 2))
                ->description('All time collections')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getRevenueChart()),

            Stat::make('This Month', 'D ' . number_format($thisMonthRevenue, 2))
                ->description($revenueTrend >= 0 ? "+{$revenueTrend}% vs last month" : "{$revenueTrend}% vs last month")
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Pending Payments', 'D ' . number_format($pendingPayments, 2))
                ->description("{$pendingCount} payments pending")
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 5 ? 'danger' : 'warning'),

            Stat::make('Monthly Rent (Active)', 'D ' . number_format($monthlyRentTotal, 2))
                ->description("{$activeLeases} active leases")
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
        ];
    }

    private function getRevenueChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $amount = Payment::where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $data[] = $amount / 1000; // Scale down for chart display
        }
        return $data;
    }
}
