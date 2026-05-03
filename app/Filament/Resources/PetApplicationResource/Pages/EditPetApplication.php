<?php

namespace App\Filament\Resources\PetApplicationResource\Pages;

use App\Filament\Resources\PetApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPetApplication extends EditRecord
{
    protected static string $resource = PetApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
