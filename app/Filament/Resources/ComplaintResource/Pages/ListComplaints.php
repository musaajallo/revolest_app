<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaints extends ListRecords
{
    protected static string $resource = ComplaintResource::class;

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
                    $filename = 'complaints_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Submitted By',
                            'Submitted By Email',
                            'Submitted By Role',
                            'Property Title',
                            'Property Address',
                            'About Tenant',
                            'Tenant Email',
                            'Tenant Phone',
                            'Category',
                            'Priority',
                            'Description',
                            'Status',
                            'Submitted At',
                            'Resolved At',
                            'Resolution Notes',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all complaints including soft deleted ones with relationships
                        \App\Models\Complaint::withTrashed()->with(['submittedBy', 'property', 'tenant'])->get()->each(function ($complaint) use ($file) {
                            fputcsv($file, [
                                $complaint->id,
                                $complaint->submittedBy?->name,
                                $complaint->submittedBy?->email,
                                ucwords(str_replace('_', ' ', $complaint->submittedBy?->role ?? '')),
                                $complaint->property?->title,
                                $complaint->property?->address,
                                $complaint->tenant?->name ?? 'N/A',
                                $complaint->tenant?->email ?? 'N/A',
                                $complaint->tenant?->phone ?? 'N/A',
                                ucwords(str_replace('_', ' ', $complaint->complaint_category)),
                                ucfirst($complaint->priority),
                                $complaint->description,
                                ucwords(str_replace('_', ' ', $complaint->status)),
                                $complaint->submitted_at?->format('Y-m-d H:i:s'),
                                $complaint->resolved_at?->format('Y-m-d H:i:s'),
                                $complaint->resolution_notes,
                                $complaint->created_at?->format('Y-m-d H:i:s'),
                                $complaint->updated_at?->format('Y-m-d H:i:s'),
                                $complaint->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
