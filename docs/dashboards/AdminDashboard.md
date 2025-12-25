# Admin Dashboard

## Purpose
Central hub for system owners to monitor and manage the entire platform.

## Recommended Widgets & Metrics
- Total properties, owners, agents, tenants
- Properties by status (available, rented, sold)
- Recent listings and inquiries
- Payments summary (total, overdue)
- Repair requests & complaints (open/closed)
- Lease expirations and renewals

## Filament Setup Steps
1. Create a custom dashboard page:
   ```bash
   php artisan make:filament-page AdminDashboard
   ```
2. In `app/Filament/Pages/AdminDashboard.php`, add widgets:
   ```php
   use Filament\Widgets\StatsOverviewWidget;
   // ...existing code...
   public static function getWidgets(): array
   {
       return [
           StatsOverviewWidget::make()
               ->stat('Total Properties', \App\Models\Property::count())
               ->stat('Total Owners', \App\Models\Owner::count())
               ->stat('Total Agents', \App\Models\Agent::count())
               ->stat('Total Tenants', \App\Models\Tenant::count()),
           // Add more widgets as needed
       ];
   }
   ```
3. Add charts or tables for recent activity using Filament chart/table widgets.

## Tips
- Use Filament's built-in widgets for stats, tables, and charts.
- Restrict access to admins using Filament's authorization features.
- Customize layout and add links to key resources.
