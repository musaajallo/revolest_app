<?php


namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\TenantLeaseSummaryWidget;

class TenantDashboard extends Page
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $slug = 'tenant-dashboard';
    protected static string $view = 'filament.pages.tenant-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Tenant Dashboard';

    public function getWidgets(): array
    {
        return [
            TenantLeaseSummaryWidget::class,
        ];
    }
}
