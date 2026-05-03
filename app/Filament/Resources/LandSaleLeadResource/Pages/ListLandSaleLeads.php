<?php

namespace App\Filament\Resources\LandSaleLeadResource\Pages;

use App\Filament\Resources\LandSaleLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandSaleLeads extends ListRecords
{
    protected static string $resource = LandSaleLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
