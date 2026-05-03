<?php

namespace App\Filament\Resources\PurchaseBuildPropertyLeadResource\Pages;

use App\Filament\Resources\PurchaseBuildPropertyLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseBuildPropertyLead extends EditRecord
{
    protected static string $resource = PurchaseBuildPropertyLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
