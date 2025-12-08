<?php

namespace App\Filament\Widgets;

use App\Models\Lease;
use Filament\Widgets\ChartWidget;

class LeaseStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Lease Status Overview';
    protected static ?string $description = 'Current status of all leases';
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $statuses = Lease::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are represented
        $defaultStatuses = ['active' => 0, 'expired' => 0, 'pending' => 0, 'terminated' => 0];
        $statuses = array_merge($defaultStatuses, $statuses);

        $colors = [
            'active' => '#10b981',     // Green
            'expired' => '#ef4444',    // Red
            'pending' => '#f59e0b',    // Amber
            'terminated' => '#6b7280', // Gray
        ];

        $chartColors = [];
        foreach (array_keys($statuses) as $status) {
            $chartColors[] = $colors[$status] ?? '#6b7280';
        }

        return [
            'datasets' => [
                [
                    'data' => array_values($statuses),
                    'backgroundColor' => $chartColors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($statuses)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
