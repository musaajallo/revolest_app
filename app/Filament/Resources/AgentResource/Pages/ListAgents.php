<?php

namespace App\Filament\Resources\AgentResource\Pages;

use App\Filament\Resources\AgentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgents extends ListRecords
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export to CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => auth()->check() && auth()->user()->role === 'super_admin')
                ->action(function () {
                    $filename = 'agents_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Unique ID',
                            'Name',
                            'Email',
                            'Phone',
                            'Address',
                            'Bio',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all agents including soft deleted ones
                        \App\Models\Agent::withTrashed()->get()->each(function ($agent) use ($file) {
                            fputcsv($file, [
                                $agent->id,
                                $agent->unique_id,
                                $agent->name,
                                $agent->email,
                                $agent->phone,
                                $agent->address,
                                $agent->bio,
                                $agent->created_at?->format('Y-m-d H:i:s'),
                                $agent->updated_at?->format('Y-m-d H:i:s'),
                                $agent->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
