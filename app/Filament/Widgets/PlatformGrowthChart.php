<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use App\Models\Owner;
use App\Models\Agent;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PlatformGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Platform Growth';
    protected static ?string $description = 'Monthly registrations over the past 12 months';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = [];
        $tenantData = [];
        $ownerData = [];
        $agentData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $tenantData[] = Tenant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $ownerData[] = Owner::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $agentData[] = Agent::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tenants',
                    'data' => $tenantData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Owners',
                    'data' => $ownerData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Agents',
                    'data' => $agentData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
