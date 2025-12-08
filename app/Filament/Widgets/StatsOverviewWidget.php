<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\Owner;
use App\Models\Agent;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate trends (comparing this month to last month)
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Properties
        $totalProperties = Property::count();
        $propertiesThisMonth = Property::where('created_at', '>=', $startOfMonth)->count();
        $propertiesLastMonth = Property::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $propertyTrend = $propertiesLastMonth > 0 
            ? round((($propertiesThisMonth - $propertiesLastMonth) / $propertiesLastMonth) * 100, 1)
            : ($propertiesThisMonth > 0 ? 100 : 0);

        // Tenants
        $totalTenants = Tenant::count();
        $tenantsThisMonth = Tenant::where('created_at', '>=', $startOfMonth)->count();
        $tenantsLastMonth = Tenant::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $tenantTrend = $tenantsLastMonth > 0 
            ? round((($tenantsThisMonth - $tenantsLastMonth) / $tenantsLastMonth) * 100, 1)
            : ($tenantsThisMonth > 0 ? 100 : 0);

        // Owners
        $totalOwners = Owner::count();
        $ownersThisMonth = Owner::where('created_at', '>=', $startOfMonth)->count();
        $ownersLastMonth = Owner::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $ownerTrend = $ownersLastMonth > 0 
            ? round((($ownersThisMonth - $ownersLastMonth) / $ownersLastMonth) * 100, 1)
            : ($ownersThisMonth > 0 ? 100 : 0);

        // Agents
        $totalAgents = Agent::count();
        $agentsThisMonth = Agent::where('created_at', '>=', $startOfMonth)->count();
        $agentsLastMonth = Agent::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $agentTrend = $agentsLastMonth > 0 
            ? round((($agentsThisMonth - $agentsLastMonth) / $agentsLastMonth) * 100, 1)
            : ($agentsThisMonth > 0 ? 100 : 0);

        return [
            Stat::make('Total Properties', number_format($totalProperties))
                ->description($propertyTrend >= 0 ? "+{$propertyTrend}% from last month" : "{$propertyTrend}% from last month")
                ->descriptionIcon($propertyTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($propertyTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyData(Property::class)),

            Stat::make('Total Tenants', number_format($totalTenants))
                ->description($tenantTrend >= 0 ? "+{$tenantTrend}% from last month" : "{$tenantTrend}% from last month")
                ->descriptionIcon($tenantTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tenantTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyData(Tenant::class)),

            Stat::make('Total Owners', number_format($totalOwners))
                ->description($ownerTrend >= 0 ? "+{$ownerTrend}% from last month" : "{$ownerTrend}% from last month")
                ->descriptionIcon($ownerTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ownerTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyData(Owner::class)),

            Stat::make('Total Agents', number_format($totalAgents))
                ->description($agentTrend >= 0 ? "+{$agentTrend}% from last month" : "{$agentTrend}% from last month")
                ->descriptionIcon($agentTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($agentTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyData(Agent::class)),
        ];
    }

    private function getMonthlyData(string $model): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = $model::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
