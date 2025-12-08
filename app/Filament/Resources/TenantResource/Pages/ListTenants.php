<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

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
                    $filename = 'tenants_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            'ID Number',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all tenants including soft deleted ones
                        \App\Models\Tenant::withTrashed()->get()->each(function ($tenant) use ($file) {
                            fputcsv($file, [
                                $tenant->id,
                                $tenant->unique_id,
                                $tenant->name,
                                $tenant->email,
                                $tenant->phone,
                                $tenant->address,
                                $tenant->id_number,
                                $tenant->created_at?->format('Y-m-d H:i:s'),
                                $tenant->updated_at?->format('Y-m-d H:i:s'),
                                $tenant->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
