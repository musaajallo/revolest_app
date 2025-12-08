<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

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
                    $filename = 'properties_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Title',
                            'Address',
                            'Price (GMD)',
                            'Type',
                            'Status',
                            'Bedrooms',
                            'Bathrooms',
                            'Area (sq ft)',
                            'Owner Name',
                            'Owner Email',
                            'Description',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all properties including soft deleted ones with owner relationship
                        \App\Models\Property::withTrashed()->with('owner')->get()->each(function ($property) use ($file) {
                            fputcsv($file, [
                                $property->id,
                                $property->title,
                                $property->address,
                                $property->price,
                                $property->type,
                                $property->status,
                                $property->bedrooms,
                                $property->bathrooms,
                                $property->area,
                                $property->owner?->name,
                                $property->owner?->email,
                                $property->description,
                                $property->created_at?->format('Y-m-d H:i:s'),
                                $property->updated_at?->format('Y-m-d H:i:s'),
                                $property->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
