# Owner Dashboard

## Purpose
Allows property owners to manage their properties and view performance metrics.

## Recommended Widgets & Metrics
- Properties owned (count, status)
- Income summary (total, by month)
- Pending approvals (properties, tenants)
- Recent repair requests and complaints
- Lease expirations and renewals

## Filament Setup Steps
1. Create a custom dashboard page:
   ```bash
   php artisan make:filament-page OwnerDashboard
   ```
2. In `app/Filament/Pages/OwnerDashboard.php`, add widgets:
   ```php
   use Filament\Widgets\StatsOverviewWidget;
   // ...existing code...
   public static function getWidgets(): array
   {
       $ownerId = auth()->user()->owner->id ?? null;
       return [
           StatsOverviewWidget::make()
               ->stat('My Properties', \App\Models\Property::where('owner_id', $ownerId)->count())
               ->stat('Active Leases', \App\Models\Lease::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))->where('status', 'active')->count())
               ->stat('Total Income', \App\Models\Payment::where('owner_id', $ownerId)->sum('amount')),
           // Add more widgets as needed
       ];
   }
   ```
3. Add tables for recent repair requests, complaints, and lease expirations.

## Tips
- Filter all widgets by the authenticated owner.
- Use Filament's authorization to restrict access.
- Add quick links to property management actions.
