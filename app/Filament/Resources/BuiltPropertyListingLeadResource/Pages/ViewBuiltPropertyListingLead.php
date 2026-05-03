<?php

namespace App\Filament\Resources\BuiltPropertyListingLeadResource\Pages;

use App\Filament\Resources\BuiltPropertyListingLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBuiltPropertyListingLead extends ViewRecord
{
    protected static string $resource = BuiltPropertyListingLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
