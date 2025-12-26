<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';
    protected static ?string $description = 'Revenue collected over the past 12 months (GMD)';
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = [];
        $revenueData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');

            $revenue = Payment::where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $revenueData[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (GMD)',
                    'data' => $revenueData,
                    'backgroundColor' => [
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 0.8)',
                        'rgba(28, 71, 54, 1)',
                    ],
                    'borderColor' => '#1c4736',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return 'D ' + value.toLocaleString(); }",
                    ],
                ],
            ],
        ];
    }
}
