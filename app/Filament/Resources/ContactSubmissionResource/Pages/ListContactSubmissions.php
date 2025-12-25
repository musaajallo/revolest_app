<?php

namespace App\Filament\Resources\ContactSubmissionResource\Pages;

use App\Filament\Resources\ContactSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactSubmissions extends ListRecords
{
    protected static string $resource = ContactSubmissionResource::class;

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
                    $filename = 'contact_submissions_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            'Phone',
                            'Subject',
                            'Message',
                            'Status',
                            'Read At',
                            'Created At',
                            'Updated At'
                        ]);

                        // Get all contact submissions
                        \App\Models\ContactSubmission::all()->each(function ($submission) use ($file) {
                            fputcsv($file, [
                                $submission->id,
                                $submission->name,
                                $submission->email,
                                $submission->phone,
                                $submission->subject,
                                $submission->message,
                                ucfirst($submission->status),
                                $submission->read_at?->format('Y-m-d H:i:s'),
                                $submission->created_at?->format('Y-m-d H:i:s'),
                                $submission->updated_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
