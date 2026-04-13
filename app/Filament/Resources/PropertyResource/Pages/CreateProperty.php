<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected bool $redirectToUnits = false;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAndAddUnitsFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateAndAddUnitsFormAction(): Action
    {
        return Action::make('createAndAddUnits')
            ->label('Create & Add Units')
            ->action('createAndAddUnits')
            ->keyBindings(['mod+shift+s'])
            ->color('gray');
    }

    public function createAndAddUnits(): void
    {
        $this->redirectToUnits = true;

        $this->create();
    }

    protected function getRedirectUrl(): string
    {
        if ($this->redirectToUnits) {
            return static::getResource()::getUrl('view', ['record' => $this->record]);
        }

        return static::getResource()::getUrl('index');
    }
}
