<?php

namespace App\Filament\Resources\LandSaleLeadResource\Pages;

use App\Filament\Resources\LandSaleLeadResource;
use App\Models\LandSaleLead;
use App\Support\CsvDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandSaleLeads extends ListRecords
{
    protected static string $resource = LandSaleLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CsvDownload::action(
                'land_sale_leads',
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
                fn () => LandSaleLead::withTrashed()
                    ->orderBy('submitted_at', 'desc')
                    ->cursor()
                    ->map(fn (LandSaleLead $l) => [
                        $l->full_name,
                        $l->submitted_at?->format('Y-m-d'),
                        $l->phone_primary,
                        $l->email,
                        'Land Sale',
                        'Sell plot',
                        null,
                        $l->land_history,
                        $l->land_size,
                        $l->land_location,
                        $l->asking_price,
                        $l->land_location,
                        $l->budget_min,
                        $l->budget_max,
                        null,
                        $l->bathrooms,
                        null,
                        $l->property_condition,
                        null,
                        $l->intended_use,
                        $l->referred_by_name ?: $l->referral_source,
                        $l->status,
                    ])
            ),
        ];
    }
}
