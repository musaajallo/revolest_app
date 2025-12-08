<?php

namespace App\Filament\Resources\LeaseResource\Pages;

use App\Filament\Resources\LeaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeases extends ListRecords
{
    protected static string $resource = LeaseResource::class;

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
                    $filename = 'leases_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            'Start Date',
                            'End Date',
                            'Rent Amount (GMD)',
                            'Security Deposit (GMD)',
                            'Status',
                            'Payment Frequency',
                            'Contract File',
                            'Terms',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all leases including soft deleted ones with relationships
                        \App\Models\Lease::withTrashed()->with(['property', 'tenant'])->get()->each(function ($lease) use ($file) {
                            fputcsv($file, [
                                $lease->id,
                                $lease->property?->title,
                                $lease->property?->address,
                                $lease->tenant?->name,
                                $lease->tenant?->email,
                                $lease->tenant?->phone,
                                $lease->start_date?->format('Y-m-d'),
                                $lease->end_date?->format('Y-m-d'),
                                $lease->rent_amount,
                                $lease->security_deposit,
                                $lease->status,
                                $lease->payment_frequency,
                                $lease->contract_file,
                                $lease->terms,
                                $lease->created_at?->format('Y-m-d H:i:s'),
                                $lease->updated_at?->format('Y-m-d H:i:s'),
                                $lease->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
