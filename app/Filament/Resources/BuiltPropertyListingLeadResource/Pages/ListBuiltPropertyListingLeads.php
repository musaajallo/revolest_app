<?php

namespace App\Filament\Resources\BuiltPropertyListingLeadResource\Pages;

use App\Filament\Resources\BuiltPropertyListingLeadResource;
use App\Models\BuiltPropertyListingLead;
use App\Support\CsvDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuiltPropertyListingLeads extends ListRecords
{
    protected static string $resource = BuiltPropertyListingLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CsvDownload::action(
                'built_property_listing_leads',
                [
                    'Client Name', 'Date', 'Contact', 'Email',
                    'Service Category', 'Purpose / Client Needs',
                    'Property Type', 'Description',
                    'Pty Size', 'Property Address', 'Price (D)',
                    'Preferred Location(s)',
                    'Budget (Min)', 'Budget (Max)',
                    'Bed', 'Bath', 'Furnished',
                    'Property Condition', 'Rent. Duration', 'Intended Use',
                    'Referral Sources', 'Status',
                ],
                fn () => BuiltPropertyListingLead::withTrashed()
                    ->orderBy('submitted_at', 'desc')
                    ->cursor()
                    ->map(fn (BuiltPropertyListingLead $l) => [
                        trim("{$l->first_name} {$l->last_name}"),
                        $l->submitted_at?->format('Y-m-d'),
                        $l->phone,
                        $l->email,
                        'Built Property Listing',
                        'List built property for sale',
                        $l->property_type,
                        $l->showing_instructions,
                        $l->plot_size ?: $l->land_dimension,
                        $l->property_address,
                        $l->asking_price,
                        null,
                        $l->budget_min,
                        $l->budget_max,
                        $l->bedrooms ?: $l->bedrooms_detail,
                        $l->bathrooms ?: $l->bathrooms_detail,
                        $l->furnished ? 'Yes' : ($l->furnished === false ? 'No' : null),
                        $l->property_condition ?: $l->property_status,
                        null,
                        $l->intended_use,
                        $l->referred_by_name ?: $l->referral_name,
                        $l->status,
                    ])
            ),
        ];
    }
}
