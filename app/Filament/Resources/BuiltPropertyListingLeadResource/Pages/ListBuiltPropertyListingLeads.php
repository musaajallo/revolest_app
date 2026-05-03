<?php

namespace App\Filament\Resources\BuiltPropertyListingLeadResource\Pages;

use App\Filament\Resources\BuiltPropertyListingLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuiltPropertyListingLeads extends ListRecords
{
    protected static string $resource = BuiltPropertyListingLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
