# Agent Dashboard

## Purpose
Enables agents to track their listings, inquiries, and performance.

## Recommended Widgets & Metrics
- Listings managed (count, status)
- Deals closed (count)
- Recent inquiries
- Agent performance (conversion rate, response time)

## Filament Setup Steps
1. Create a custom dashboard page:
   ```bash
   php artisan make:filament-page AgentDashboard
   ```
2. In `app/Filament/Pages/AgentDashboard.php`, add widgets:
   ```php
   use Filament\Widgets\StatsOverviewWidget;
   // ...existing code...
   public static function getWidgets(): array
   {
       $agentId = auth()->user()->agent->id ?? null;
       return [
           StatsOverviewWidget::make()
               ->stat('My Listings', \App\Models\Listing::where('agent_id', $agentId)->count())
               ->stat('Closed Deals', \App\Models\Listing::where('agent_id', $agentId)->where('status', 'sold')->count())
               ->stat('Recent Inquiries', \App\Models\Inquiry::whereHas('listing', fn($q) => $q->where('agent_id', $agentId))->count()),
           // Add more widgets as needed
       ];
   }
   ```
3. Add tables for recent inquiries and listing performance.

## Tips
- Filter widgets by the authenticated agent.
- Use Filament's authorization to restrict access.
- Add quick links to manage listings and respond to inquiries.
