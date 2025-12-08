<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected int|string|null $defaultTableRecordsPerPageSelectOption = 25;

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
                    $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function() {
                        $file = fopen('php://output', 'w');

                        // CSV Headers
                        fputcsv($file, [
                            'ID',
                            'Name',
                            'Email',
                            'Role',
                            'Email Verified At',
                            'Created At',
                            'Updated At',
                            'Deleted At'
                        ]);

                        // Get all users including soft deleted ones
                        \App\Models\User::withTrashed()->get()->each(function ($user) use ($file) {
                            fputcsv($file, [
                                $user->id,
                                $user->name,
                                $user->email,
                                ucwords(str_replace('_', ' ', $user->role)),
                                $user->email_verified_at?->format('Y-m-d H:i:s'),
                                $user->created_at?->format('Y-m-d H:i:s'),
                                $user->updated_at?->format('Y-m-d H:i:s'),
                                $user->deleted_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
