<?php

namespace App\Filament\Resources\PetApplicationResource\Pages;

use App\Filament\Resources\PetApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPetApplication extends ViewRecord
{
    protected static string $resource = PetApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
