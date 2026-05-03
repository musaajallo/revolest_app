<?php

namespace App\Filament\Resources\LandPurchaseLeadResource\Pages;

use App\Filament\Resources\LandPurchaseLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandPurchaseLead extends EditRecord
{
    protected static string $resource = LandPurchaseLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
