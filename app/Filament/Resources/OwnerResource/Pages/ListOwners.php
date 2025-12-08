<?php

namespace App\Filament\Resources\OwnerResource\Pages;

use App\Filament\Resources\OwnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOwners extends ListRecords
{
    protected static string $resource = OwnerResource::class;

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
                    $filename = 'owners_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all owners including soft deleted ones
                        \App\Models\Owner::withTrashed()->get()->each(function ($owner) use ($file) {
                            fputcsv($file, [
                                $owner->id,
                                $owner->unique_id,
                                $owner->name,
                                $owner->email,
                                $owner->phone,
                                $owner->address,
                                $owner->created_at?->format('Y-m-d H:i:s'),
                                $owner->updated_at?->format('Y-m-d H:i:s'),
                                $owner->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
