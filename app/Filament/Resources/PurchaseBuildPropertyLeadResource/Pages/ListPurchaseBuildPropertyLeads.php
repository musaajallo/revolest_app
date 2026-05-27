<?php

namespace App\Filament\Resources\PurchaseBuildPropertyLeadResource\Pages;

use App\Filament\Resources\PurchaseBuildPropertyLeadResource;
use App\Models\PurchaseBuildPropertyLead;
use App\Support\CsvDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseBuildPropertyLeads extends ListRecords
{
    protected static string $resource = PurchaseBuildPropertyLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CsvDownload::action(
                'purchase_build_property_leads',
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
                fn () => PurchaseBuildPropertyLead::withTrashed()
                    ->orderBy('submitted_at', 'desc')
                    ->cursor()
                    ->map(fn (PurchaseBuildPropertyLead $l) => [
                        $l->full_name,
                        $l->submitted_at?->format('Y-m-d'),
                        $l->phone_primary,
                        $l->email,
                        'Purchase Built Property',
                        'Buy built property',
                        $l->property_type,
                        $l->other_considerations,
                        $l->plot_size,
                        $l->current_address,
                        null,
                        $l->preferred_location,
                        $l->budget_min,
                        $l->budget_max,
                        $l->bedrooms ?: $l->bedrooms_bathrooms,
                        $l->bathrooms,
                        null,
                        $l->property_condition ?: $l->build_status,
                        null,
                        $l->intended_use ?: $l->use_purpose,
                        $l->referred_by_name ?: $l->referral_name,
                        $l->status,
                    ])
            ),
        ];
    }
}
