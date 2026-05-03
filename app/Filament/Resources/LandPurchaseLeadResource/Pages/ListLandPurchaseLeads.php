<?php

namespace App\Filament\Resources\LandPurchaseLeadResource\Pages;

use App\Filament\Resources\LandPurchaseLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandPurchaseLeads extends ListRecords
{
    protected static string $resource = LandPurchaseLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
