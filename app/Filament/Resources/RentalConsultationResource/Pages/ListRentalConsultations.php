<?php

namespace App\Filament\Resources\RentalConsultationResource\Pages;

use App\Filament\Resources\RentalConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalConsultations extends ListRecords
{
    protected static string $resource = RentalConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
