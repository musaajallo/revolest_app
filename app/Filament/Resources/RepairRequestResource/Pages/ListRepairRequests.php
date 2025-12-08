<?php

namespace App\Filament\Resources\RepairRequestResource\Pages;

use App\Filament\Resources\RepairRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepairRequests extends ListRecords
{
    protected static string $resource = RepairRequestResource::class;

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
                    $filename = 'repair_requests_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Property Title',
                            'Property Address',
                            'Tenant Name',
                            'Tenant Email',
                            'Tenant Phone',
                            'Description',
                            'Status',
                            'Priority',
                            'Submitted At',
                            'Resolved At',
                            'Resolution Notes',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all repair requests including soft deleted ones with relationships
                        \App\Models\RepairRequest::withTrashed()->with(['property', 'tenant'])->get()->each(function ($repairRequest) use ($file) {
                            fputcsv($file, [
                                $repairRequest->id,
                                $repairRequest->property?->title,
                                $repairRequest->property?->address,
                                $repairRequest->tenant?->name,
                                $repairRequest->tenant?->email,
                                $repairRequest->tenant?->phone,
                                $repairRequest->description,
                                $repairRequest->status,
                                $repairRequest->priority,
                                $repairRequest->submitted_at?->format('Y-m-d H:i:s'),
                                $repairRequest->resolved_at?->format('Y-m-d H:i:s'),
                                $repairRequest->resolution_notes,
                                $repairRequest->created_at?->format('Y-m-d H:i:s'),
                                $repairRequest->updated_at?->format('Y-m-d H:i:s'),
                                $repairRequest->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
