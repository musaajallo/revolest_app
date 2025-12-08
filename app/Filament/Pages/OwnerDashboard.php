<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class OwnerDashboard extends \Filament\Pages\Page
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $slug = 'owner-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $title = 'Owner Dashboard';
    protected static string $view = 'filament.pages.owner-dashboard';
}
