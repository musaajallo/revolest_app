<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label('View Page')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => match($this->record->slug) {
                    'home' => '/',
                    'contact' => '/contact',
                    'properties' => '/properties',
                    'agents' => '/agents',
                    default => '/' . $this->record->slug,
                })
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
