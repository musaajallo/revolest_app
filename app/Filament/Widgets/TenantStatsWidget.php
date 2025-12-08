<?php

namespace App\Filament\Widgets;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\RepairRequest;
use App\Models\Complaint;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TenantStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $tenant = $user?->tenant ?? \App\Models\Tenant::where('user_id', $user?->id)->first();

        if (!$tenant) {
            return [];
        }

        // Active Lease
        $activeLease = $tenant->leases()->where('status', 'active')->first();
        $leaseStatus = $activeLease ? 'Active' : 'No Active Lease';
        $leaseColor = $activeLease ? 'success' : 'gray';

        // Upcoming Payment
        $nextPaymentDue = null;
        $daysUntilPayment = null;
        if ($activeLease) {
            $nextPaymentDate = Carbon::parse($activeLease->start_date)
                ->addMonths(Carbon::now()->diffInMonths($activeLease->start_date) + 1);
            $daysUntilPayment = Carbon::now()->diffInDays($nextPaymentDate, false);
            $nextPaymentDue = $nextPaymentDate->format('M j, Y');
        }

        // Total Payments Made
        $totalPaid = $tenant->payments()
            ->where('status', 'completed')
            ->sum('amount');

        // Pending Repair Requests
        $pendingRepairs = $tenant->repairRequests()
            ->where('status', 'pending')
            ->count();

        // Open Complaints
        $openComplaints = $tenant->complaints()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        return [
            Stat::make('Lease Status', $leaseStatus)
                ->description($activeLease ? "Expires on " . Carbon::parse($activeLease->end_date)->format('M j, Y') : 'No active lease')
                ->descriptionIcon($activeLease ? 'heroicon-m-document-check' : 'heroicon-m-document')
                ->color($leaseColor),

            Stat::make('Next Payment Due', $nextPaymentDue ?? 'N/A')
                ->description($daysUntilPayment !== null ?
                    ($daysUntilPayment > 0 ? "In {$daysUntilPayment} days" : ($daysUntilPayment == 0 ? 'Due today' : 'Overdue'))
                    : 'No active lease')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($daysUntilPayment !== null && $daysUntilPayment < 7 ? 'warning' : 'primary'),

            Stat::make('Total Paid', 'D' . number_format($totalPaid, 2))
                ->description('Lifetime payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Requests', number_format($pendingRepairs + $openComplaints))
                ->description("{$pendingRepairs} repairs, {$openComplaints} complaints")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($pendingRepairs + $openComplaints > 0 ? 'warning' : 'success'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'tenant';
    }
}
