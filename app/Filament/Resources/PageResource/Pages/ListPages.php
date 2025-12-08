<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

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
                    $filename = 'pages_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            'Slug',
                            'Meta Title',
                            'Meta Description',
                            'Is Published',
                            'Content',
                            'Created At',
                            'Updated At'
                        ]);

                        // Get all pages
                        \App\Models\Page::all()->each(function ($page) use ($file) {
                            // Convert content array to JSON string for CSV
                            $contentJson = is_array($page->content) ? json_encode($page->content) : $page->content;

                            fputcsv($file, [
                                $page->id,
                                $page->title,
                                $page->slug,
                                $page->meta_title,
                                $page->meta_description,
                                $page->is_published ? 'Yes' : 'No',
                                $contentJson,
                                $page->created_at?->format('Y-m-d H:i:s'),
                                $page->updated_at?->format('Y-m-d H:i:s')
                            ]);
                        });

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
