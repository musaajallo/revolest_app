<?php

namespace App\Filament\Resources\LandPurchaseLeadResource\Pages;

use App\Filament\Resources\LandPurchaseLeadResource;
use App\Models\LandPurchaseLead;
use App\Support\CsvDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandPurchaseLeads extends ListRecords
{
    protected static string $resource = LandPurchaseLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CsvDownload::action(
                'land_purchase_leads',
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
                fn () => LandPurchaseLead::withTrashed()
                    ->orderBy('submitted_at', 'desc')
                    ->cursor()
                    ->map(fn (LandPurchaseLead $l) => [
                        $l->full_name,
                        $l->submitted_at?->format('Y-m-d'),
                        $l->phone,
                        $l->email,
                        'Land Purchase',
                        'Buy plot',
                        $l->land_type,
                        $l->special_requirements,
                        $l->plot_size,
                        $l->address,
                        $l->budget,
                        $l->preferred_locations,
                        $l->budget_min,
                        $l->budget_max,
                        null,
                        $l->bathrooms,
                        null,
                        $l->property_condition,
                        null,
                        $l->intended_use,
                        $l->referred_by_name,
                        $l->status,
                    ])
            ),
        ];
    }
}
