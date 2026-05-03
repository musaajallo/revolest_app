<?php

namespace App\Filament\Resources\LandPurchaseLeadResource\Pages;

use App\Filament\Resources\LandPurchaseLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLandPurchaseLead extends ViewRecord
{
    protected static string $resource = LandPurchaseLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
