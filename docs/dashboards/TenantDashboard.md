# Tenant Dashboard

## Purpose
Allows tenants to manage leases, payments, requests, and view their activity.

## Recommended Widgets & Metrics
- Active leases (count)
- Payments made (total, recent)
- Repair requests (open/closed)
- Complaints (open/closed)
- Lease renewal status

## Filament Setup Steps
1. Create a custom dashboard page:
   ```bash
   php artisan make:filament-page TenantDashboard
   ```
2. In `app/Filament/Pages/TenantDashboard.php`, add widgets:
   ```php
   use Filament\Widgets\StatsOverviewWidget;
   // ...existing code...
   public static function getWidgets(): array
   {
       $tenantId = auth()->user()->tenant->id ?? null;
       return [
           StatsOverviewWidget::make()
               ->stat('Active Leases', \App\Models\Lease::where('tenant_id', $tenantId)->where('status', 'active')->count())
               ->stat('Payments Made', \App\Models\Payment::where('tenant_id', $tenantId)->sum('amount'))
               ->stat('Open Repair Requests', \App\Models\RepairRequest::where('tenant_id', $tenantId)->where('status', 'new')->count())
               ->stat('Open Complaints', \App\Models\Complaint::where('tenant_id', $tenantId)->where('status', 'new')->count()),
           // Add more widgets as needed
       ];
   }
   ```
3. Add tables for recent payments, requests, and lease renewals.

## Tips
- Filter widgets by the authenticated tenant.
- Use Filament's authorization to restrict access.
- Add quick links to submit requests, view receipts, and renew leases.
