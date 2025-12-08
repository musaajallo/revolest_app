<?php

namespace App\Filament\Resources\ReceiptResource\Pages;

use App\Filament\Resources\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;

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
                    $filename = 'receipts_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Payment ID',
                            'Lease ID',
                            'Property Title',
                            'Tenant Name',
                            'Amount (GMD)',
                            'Issued At',
                            'File',
                            'Description',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all receipts including soft deleted ones with relationships
                        \App\Models\Receipt::withTrashed()->with(['payment.lease.property', 'payment.tenant'])->get()->each(function ($receipt) use ($file) {
                            fputcsv($file, [
                                $receipt->id,
                                $receipt->payment_id,
                                $receipt->payment?->lease_id,
                                $receipt->payment?->lease?->property?->title,
                                $receipt->payment?->tenant?->name,
                                $receipt->amount,
                                $receipt->issued_at?->format('Y-m-d H:i:s'),
                                $receipt->file,
                                $receipt->description,
                                $receipt->created_at?->format('Y-m-d H:i:s'),
                                $receipt->updated_at?->format('Y-m-d H:i:s'),
                                $receipt->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
