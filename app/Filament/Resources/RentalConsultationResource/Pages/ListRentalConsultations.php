<?php

namespace App\Filament\Resources\RentalConsultationResource\Pages;

use App\Filament\Resources\RentalConsultationResource;
use App\Models\RentalConsultation;
use App\Support\CsvDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalConsultations extends ListRecords
{
    protected static string $resource = RentalConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CsvDownload::action(
                'rental_consultations',
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
                fn () => RentalConsultation::withTrashed()
                    ->orderBy('submitted_at', 'desc')
                    ->cursor()
                    ->map(fn (RentalConsultation $l) => [
                        $l->full_name,
                        $l->submitted_at?->format('Y-m-d'),
                        $l->phone,
                        $l->email,
                        'Rental',
                        'Looking for rent',
                        $l->property_kind,
                        $l->property_suggestions ?: $l->desired_facilities,
                        $l->plot_size,
                        null,
                        null,
                        $l->preferred_locations,
                        $l->budget_min,
                        $l->budget_max,
                        $l->bedrooms,
                        $l->bathrooms,
                        $l->furnished ? 'Yes' : ($l->furnished === false ? 'No' : null),
                        $l->property_condition,
                        $l->rental_duration,
                        $l->intended_use,
                        $l->referred_by_name ?: $l->referral_name,
                        $l->status,
                    ])
            ),
        ];
    }
}
