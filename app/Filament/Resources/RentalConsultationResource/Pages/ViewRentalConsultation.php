<?php

namespace App\Filament\Resources\RentalConsultationResource\Pages;

use App\Filament\Resources\RentalConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRentalConsultation extends ViewRecord
{
    protected static string $resource = RentalConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
