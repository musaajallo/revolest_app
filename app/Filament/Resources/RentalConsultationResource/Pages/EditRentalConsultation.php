<?php

namespace App\Filament\Resources\RentalConsultationResource\Pages;

use App\Filament\Resources\RentalConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalConsultation extends EditRecord
{
    protected static string $resource = RentalConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
