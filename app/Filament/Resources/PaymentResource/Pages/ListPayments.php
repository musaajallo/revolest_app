<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

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
                    $filename = 'payments_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Lease ID',
                            'Property Title',
                            'Tenant Name',
                            'Tenant Email',
                            'Owner Name',
                            'Owner Email',
                            'Amount (GMD)',
                            'Payment Date',
                            'Method',
                            'Status',
                            'Reference Number',
                            'Receipt File',
                            'Notes',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all payments including soft deleted ones with relationships
                        \App\Models\Payment::withTrashed()->with(['lease.property', 'tenant', 'owner'])->get()->each(function ($payment) use ($file) {
                            fputcsv($file, [
                                $payment->id,
                                $payment->lease_id,
                                $payment->lease?->property?->title,
                                $payment->tenant?->name,
                                $payment->tenant?->email,
                                $payment->owner?->name,
                                $payment->owner?->email,
                                $payment->amount,
                                $payment->payment_date?->format('Y-m-d'),
                                $payment->method,
                                $payment->status,
                                $payment->reference_number,
                                $payment->receipt_file,
                                $payment->notes,
                                $payment->created_at?->format('Y-m-d H:i:s'),
                                $payment->updated_at?->format('Y-m-d H:i:s'),
                                $payment->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
