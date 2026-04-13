<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\Listing;
use App\Models\Property;
use App\Models\Lease;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class CircularStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.circular-stats-widget';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';
    private const MARKET_STATUSES = ['for_rent', 'for_sale'];

    public function getViewData(): array
    {
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        // Inquiry Stats
        $totalInquiries = Inquiry::count();
        $newInquiries = Inquiry::where('created_at', '>=', $thirtyDaysAgo)->count();
        $respondedInquiries = Inquiry::where('status', 'responded')->count();

        // Listing Stats
        $totalListings = Listing::count();
        $activeListings = Listing::whereIn('status', self::MARKET_STATUSES)->count();
        $newListings = Listing::where('created_at', '>=', $thirtyDaysAgo)->count();

        // Occupancy Rate
        $totalProperties = Property::count();
        $occupiedProperties = Lease::where('status', 'active')
            ->distinct('property_id')
            ->count('property_id');
        $occupancyRate = $totalProperties > 0
            ? round(($occupiedProperties / $totalProperties) * 100, 1)
            : 0;

        return [
            'inquiries' => [
                'total' => $totalInquiries,
                'new' => $newInquiries,
                'responded' => $respondedInquiries,
                'percentage' => $totalInquiries > 0 ? round(($respondedInquiries / $totalInquiries) * 100) : 0,
            ],
            'listings' => [
                'total' => $totalListings,
                'active' => $activeListings,
                'new' => $newListings,
                'percentage' => $totalListings > 0 ? round(($activeListings / $totalListings) * 100) : 0,
            ],
            'occupancy' => [
                'rate' => $occupancyRate,
                'occupied' => $occupiedProperties,
                'total' => $totalProperties,
            ],
        ];
    }
}
