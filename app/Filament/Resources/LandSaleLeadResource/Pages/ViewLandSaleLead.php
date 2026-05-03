<?php

namespace App\Filament\Resources\LandSaleLeadResource\Pages;

use App\Filament\Resources\LandSaleLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLandSaleLead extends ViewRecord
{
    protected static string $resource = LandSaleLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
