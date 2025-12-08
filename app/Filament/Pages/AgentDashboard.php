<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class AgentDashboard extends \Filament\Pages\Page
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $slug = 'agent-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Agent Dashboard';
    protected static string $view = 'filament.pages.agent-dashboard';
}
