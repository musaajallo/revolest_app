<?php

namespace App\Filament\Resources\PurchaseBuildPropertyLeadResource\Pages;

use App\Filament\Resources\PurchaseBuildPropertyLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseBuildPropertyLead extends ViewRecord
{
    protected static string $resource = PurchaseBuildPropertyLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
