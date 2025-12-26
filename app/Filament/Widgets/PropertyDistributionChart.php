<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PropertyDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Properties by Type';
    protected static ?string $description = 'Distribution of property types';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $types = Property::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Default types if no data
        if (empty($types)) {
            $types = [
                'house' => 0,
                'apartment' => 0,
                'land' => 0,
                'commercial' => 0,
            ];
        }

        $colors = [
            '#1c4736', // Primary green
            '#3b82f6', // Blue
            '#10b981', // Green
            '#f59e0b', // Amber
            '#8b5cf6', // Purple
            '#ec4899', // Pink
            '#06b6d4', // Cyan
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($types),
                    'backgroundColor' => array_slice($colors, 0, count($types)),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($types)),
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
