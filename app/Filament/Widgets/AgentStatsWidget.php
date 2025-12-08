<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Inquiry;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AgentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $agent = $user?->agent ?? \App\Models\Agent::where('user_id', $user?->id)->first();

        if (!$agent) {
            return [];
        }

        // Calculate trends
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Total Listings
        $totalListings = $agent->listings()->count();
        $listingsThisMonth = $agent->listings()->where('created_at', '>=', $startOfMonth)->count();
        $listingsLastMonth = $agent->listings()->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $listingTrend = $listingsLastMonth > 0
            ? round((($listingsThisMonth - $listingsLastMonth) / $listingsLastMonth) * 100, 1)
            : ($listingsThisMonth > 0 ? 100 : 0);

        // Active Listings
        $activeListings = $agent->listings()->where('status', 'active')->count();

        // Total Inquiries
        $totalInquiries = Inquiry::whereHas('listing', function ($query) use ($agent) {
            $query->where('agent_id', $agent->id);
        })->count();

        $inquiriesThisMonth = Inquiry::whereHas('listing', function ($query) use ($agent) {
            $query->where('agent_id', $agent->id);
        })->where('created_at', '>=', $startOfMonth)->count();

        $inquiriesLastMonth = Inquiry::whereHas('listing', function ($query) use ($agent) {
            $query->where('agent_id', $agent->id);
        })->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $inquiryTrend = $inquiriesLastMonth > 0
            ? round((($inquiriesThisMonth - $inquiriesLastMonth) / $inquiriesLastMonth) * 100, 1)
            : ($inquiriesThisMonth > 0 ? 100 : 0);

        // Pending Inquiries
        $pendingInquiries = Inquiry::whereHas('listing', function ($query) use ($agent) {
            $query->where('agent_id', $agent->id);
        })->where('status', 'pending')->count();

        return [
            Stat::make('Total Listings', number_format($totalListings))
                ->description($listingTrend >= 0 ? "+{$listingTrend}% from last month" : "{$listingTrend}% from last month")
                ->descriptionIcon($listingTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($listingTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyListingsData($agent)),

            Stat::make('Active Listings', number_format($activeListings))
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Inquiries', number_format($totalInquiries))
                ->description($inquiryTrend >= 0 ? "+{$inquiryTrend}% from last month" : "{$inquiryTrend}% from last month")
                ->descriptionIcon($inquiryTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($inquiryTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyInquiriesData($agent)),

            Stat::make('Pending Inquiries', number_format($pendingInquiries))
                ->description('Awaiting response')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }

    private function getMonthlyListingsData($agent): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = $agent->listings()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getMonthlyInquiriesData($agent): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Inquiry::whereHas('listing', function ($query) use ($agent) {
                $query->where('agent_id', $agent->id);
            })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'agent';
    }
}
