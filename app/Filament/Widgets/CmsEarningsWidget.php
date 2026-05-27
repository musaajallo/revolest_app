<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CmsEarningsWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $startOfQuarter = $now->copy()->firstOfQuarter();

        $base = Payment::query()
            ->where('status', 'complete')
            ->whereIn('purpose', ['rent']);

        $month = (clone $base)->where('payment_date', '>=', $startOfMonth)->sum('commission_amount');
        $quarter = (clone $base)->where('payment_date', '>=', $startOfQuarter)->sum('commission_amount');
        $year = (clone $base)->where('payment_date', '>=', $startOfYear)->sum('commission_amount');

        $outstanding = Payment::query()
            ->whereNotNull('expected_amount')
            ->whereColumn('amount', '<', 'expected_amount')
            ->selectRaw('SUM(expected_amount - amount) AS owed')
            ->value('owed') ?? 0;

        return [
            Stat::make('CMS Earnings — Month', 'D' . number_format((float) $month, 2))
                ->description($startOfMonth->format('M Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('CMS Earnings — Quarter', 'D' . number_format((float) $quarter, 2))
                ->description('Q' . $now->quarter . ' ' . $now->year)
                ->color('success'),
            Stat::make('CMS Earnings — Year', 'D' . number_format((float) $year, 2))
                ->description((string) $now->year)
                ->color('success'),
            Stat::make('Outstanding Balances', 'D' . number_format((float) $outstanding, 2))
                ->description('Across all leases')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($outstanding > 0 ? 'danger' : 'gray'),
        ];
    }

    public static function canView(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && in_array($user->role, ['super_admin', 'admin']);
    }
}
