<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

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
                    $filename = 'inquiries_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Listing ID',
                            'Property Title',
                            'Property Address',
                            'Name',
                            'Email',
                            'Phone',
                            'Message',
                            'Status',
                            'Created At',
                            'Updated At'
                        ]);

                        // Get all inquiries with relationships
                        \App\Models\Inquiry::with(['listing.property'])->get()->each(function ($inquiry) use ($file) {
                            fputcsv($file, [
                                $inquiry->id,
                                $inquiry->listing_id,
                                $inquiry->listing?->property?->title,
                                $inquiry->listing?->property?->address,
                                $inquiry->name,
                                $inquiry->email,
                                $inquiry->phone,
                                $inquiry->message,
                                $inquiry->status,
                                $inquiry->created_at?->format('Y-m-d H:i:s'),
                                $inquiry->updated_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
