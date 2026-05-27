<?php

namespace App\Support;

use Closure;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

/**
 * Shared helper for streaming CSV exports from Filament list pages.
 * Used by PaymentResource and all 5 lead resources so the column order in
 * the export mirrors the corresponding sheet in Revolest's Excel workbooks.
 */
class CsvDownload
{
    public static function action(
        string $filenamePrefix,
        array $headers,
        Closure $rowsBuilder,
        array $allowedRoles = ['super_admin', 'admin']
    ): Action {
        return Action::make('export')
            ->label('Export to CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->visible(function () use ($allowedRoles) {
                $user = Auth::user();
                return $user && in_array($user->role, $allowedRoles, true);
            })
            ->action(function () use ($filenamePrefix, $headers, $rowsBuilder) {
                $filename = "{$filenamePrefix}_" . now()->format('Y-m-d_H-i-s') . '.csv';

                $httpHeaders = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ];

                $callback = function () use ($headers, $rowsBuilder) {
                    $f = fopen('php://output', 'w');
                    fputcsv($f, $headers);
                    foreach ($rowsBuilder() as $row) {
                        fputcsv($f, $row);
                    }
                    fclose($f);
                };

                return response()->stream($callback, 200, $httpHeaders);
            });
    }
}
