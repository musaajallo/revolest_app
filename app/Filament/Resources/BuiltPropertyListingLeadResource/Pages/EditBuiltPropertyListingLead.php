<?php

namespace App\Filament\Resources\BuiltPropertyListingLeadResource\Pages;

use App\Filament\Resources\BuiltPropertyListingLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuiltPropertyListingLead extends EditRecord
{
    protected static string $resource = BuiltPropertyListingLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
