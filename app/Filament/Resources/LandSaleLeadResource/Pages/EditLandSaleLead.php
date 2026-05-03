<?php

namespace App\Filament\Resources\LandSaleLeadResource\Pages;

use App\Filament\Resources\LandSaleLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandSaleLead extends EditRecord
{
    protected static string $resource = LandSaleLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
