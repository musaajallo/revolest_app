<?php

namespace App\Filament\Resources\PetApplicationResource\Pages;

use App\Filament\Resources\PetApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPetApplications extends ListRecords
{
    protected static string $resource = PetApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
