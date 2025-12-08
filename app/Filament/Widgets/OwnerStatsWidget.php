<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\RepairRequest;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OwnerStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $owner = $user?->owner ?? \App\Models\Owner::where('user_id', $user?->id)->first();

        if (!$owner) {
            return [];
        }

        // Total Properties
        $totalProperties = $owner->properties()->count();

        // Active Leases
        $activeLeases = Lease::whereHas('property', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })->where('status', 'active')->count();

        // Total Revenue (all time)
        $totalRevenue = Payment::where('owner_id', $owner->id)
            ->where('status', 'completed')
            ->sum('amount');

        // Revenue This Month
        $revenueThisMonth = Payment::where('owner_id', $owner->id)
            ->where('status', 'completed')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        // Revenue Last Month
        $revenueLastMonth = Payment::where('owner_id', $owner->id)
            ->where('status', 'completed')
            ->whereMonth('payment_date', Carbon::now()->subMonth()->month)
            ->whereYear('payment_date', Carbon::now()->subMonth()->year)
            ->sum('amount');

        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // Pending Repairs
        $pendingRepairs = RepairRequest::whereHas('property', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })->where('status', 'pending')->count();

        return [
            Stat::make('Total Properties', number_format($totalProperties))
                ->description('Properties owned')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Active Leases', number_format($activeLeases))
                ->description('Currently occupied')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success'),

            Stat::make('Monthly Revenue', 'D' . number_format($revenueThisMonth, 2))
                ->description($revenueTrend >= 0 ? "+{$revenueTrend}% from last month" : "{$revenueTrend}% from last month")
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyRevenueData($owner)),

            Stat::make('Pending Repairs', number_format($pendingRepairs))
                ->description('Awaiting attention')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($pendingRepairs > 0 ? 'warning' : 'success'),
        ];
    }

    private function getMonthlyRevenueData($owner): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Payment::where('owner_id', $owner->id)
                ->where('status', 'completed')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $data[] = $revenue;
        }
        return $data;
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'owner';
    }
}
