<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListings extends ListRecords
{
    protected static string $resource = ListingResource::class;

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
                    $filename = 'listings_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            'Agent Name',
                            'Agent Email',
                            'Price (GMD)',
                            'Status',
                            'Published At',
                            'Description',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all listings including soft deleted ones with relationships
                        \App\Models\Listing::withTrashed()->with(['property', 'agent'])->get()->each(function ($listing) use ($file) {
                            fputcsv($file, [
                                $listing->id,
                                $listing->property?->title,
                                $listing->property?->address,
                                $listing->agent?->name,
                                $listing->agent?->email,
                                $listing->price,
                                $listing->status,
                                $listing->published_at?->format('Y-m-d H:i:s'),
                                $listing->description,
                                $listing->created_at?->format('Y-m-d H:i:s'),
                                $listing->updated_at?->format('Y-m-d H:i:s'),
                                $listing->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
