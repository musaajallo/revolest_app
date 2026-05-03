<?php

namespace App\Filament\Resources\PurchaseBuildPropertyLeadResource\Pages;

use App\Filament\Resources\PurchaseBuildPropertyLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseBuildPropertyLeads extends ListRecords
{
    protected static string $resource = PurchaseBuildPropertyLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
