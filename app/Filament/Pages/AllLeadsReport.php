<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BuiltPropertyListingLeadResource;
use App\Filament\Resources\LandPurchaseLeadResource;
use App\Filament\Resources\LandSaleLeadResource;
use App\Filament\Resources\PurchaseBuildPropertyLeadResource;
use App\Filament\Resources\RentalConsultationResource;
use App\Models\BuiltPropertyListingLead;
use App\Models\LandPurchaseLead;
use App\Models\LandSaleLead;
use App\Models\PurchaseBuildPropertyLead;
use App\Models\RentalConsultation;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class AllLeadsReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $title = 'All Client Requests';

    protected static ?string $navigationLabel = 'All Requests';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.all-leads-report';

    public ?string $filterCategory = null;

    public function getRows(): Collection
    {
        $rows = collect();

        $rows = $rows->concat(LandPurchaseLead::query()
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (LandPurchaseLead $l) => [
                'id' => $l->id,
                'category' => 'Land Purchase',
                'purpose' => 'Buy plot',
                'client_name' => $l->full_name,
                'contact' => $l->phone,
                'email' => $l->email,
                'budget_min' => $l->budget_min,
                'budget_max' => $l->budget_max,
                'budget_raw' => $l->budget,
                'location' => $l->preferred_locations,
                'plot_size' => $l->plot_size,
                'referred_by' => $l->referred_by_name,
                'status' => $l->status,
                'date' => $l->submitted_at ?? $l->created_at,
                'url' => LandPurchaseLeadResource::getUrl('view', ['record' => $l->id]),
            ]));

        $rows = $rows->concat(LandSaleLead::query()
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (LandSaleLead $l) => [
                'id' => $l->id,
                'category' => 'Land Sale',
                'purpose' => 'Sell plot',
                'client_name' => $l->full_name,
                'contact' => $l->phone_primary,
                'email' => $l->email,
                'budget_min' => $l->budget_min,
                'budget_max' => $l->budget_max,
                'budget_raw' => $l->asking_price,
                'location' => $l->land_location,
                'plot_size' => $l->land_size,
                'referred_by' => $l->referred_by_name ?? $l->referral_source,
                'status' => $l->status,
                'date' => $l->submitted_at ?? $l->created_at,
                'url' => LandSaleLeadResource::getUrl('view', ['record' => $l->id]),
            ]));

        $rows = $rows->concat(RentalConsultation::query()
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (RentalConsultation $l) => [
                'id' => $l->id,
                'category' => 'Rental',
                'purpose' => 'Looking for rent',
                'client_name' => $l->full_name,
                'contact' => $l->phone,
                'email' => $l->email,
                'budget_min' => $l->budget_min,
                'budget_max' => $l->budget_max,
                'budget_raw' => null,
                'location' => $l->preferred_locations,
                'plot_size' => $l->plot_size,
                'referred_by' => $l->referred_by_name ?? $l->referral_name,
                'status' => $l->status,
                'date' => $l->submitted_at ?? $l->created_at,
                'url' => RentalConsultationResource::getUrl('view', ['record' => $l->id]),
            ]));

        $rows = $rows->concat(BuiltPropertyListingLead::query()
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (BuiltPropertyListingLead $l) => [
                'id' => $l->id,
                'category' => 'Built Property Listing',
                'purpose' => 'List built property',
                'client_name' => trim("{$l->first_name} {$l->last_name}"),
                'contact' => $l->phone,
                'email' => $l->email,
                'budget_min' => $l->budget_min,
                'budget_max' => $l->budget_max,
                'budget_raw' => $l->asking_price,
                'location' => $l->property_address,
                'plot_size' => $l->plot_size ?? $l->land_dimension,
                'referred_by' => $l->referred_by_name ?? $l->referral_name,
                'status' => $l->status,
                'date' => $l->submitted_at ?? $l->created_at,
                'url' => BuiltPropertyListingLeadResource::getUrl('view', ['record' => $l->id]),
            ]));

        $rows = $rows->concat(PurchaseBuildPropertyLead::query()
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn (PurchaseBuildPropertyLead $l) => [
                'id' => $l->id,
                'category' => 'Buy Built Property',
                'purpose' => 'Buy built property',
                'client_name' => $l->full_name,
                'contact' => $l->phone_primary,
                'email' => $l->email,
                'budget_min' => $l->budget_min,
                'budget_max' => $l->budget_max,
                'budget_raw' => $l->budget,
                'location' => $l->preferred_location,
                'plot_size' => $l->plot_size,
                'referred_by' => $l->referred_by_name ?? $l->referral_name,
                'status' => $l->status,
                'date' => $l->submitted_at ?? $l->created_at,
                'url' => PurchaseBuildPropertyLeadResource::getUrl('view', ['record' => $l->id]),
            ]));

        $rows = $rows
            ->when($this->filterCategory, fn ($c) => $c->where('category', $this->filterCategory))
            ->sortByDesc('date')
            ->values();

        return $rows;
    }

    public function getCategories(): array
    {
        return [
            'Land Purchase',
            'Land Sale',
            'Rental',
            'Built Property Listing',
            'Buy Built Property',
        ];
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && in_array($user->role, ['super_admin', 'admin', 'agent']);
    }
}
