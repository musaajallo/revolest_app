<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgentStatsWidget;
use App\Filament\Widgets\AgentRecentInquiriesWidget;
use App\Filament\Widgets\AgentListingsWidget;
use Filament\Pages\Page;

class AgentDashboard extends Page
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $slug = 'agent-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Agent Dashboard';
    protected static ?int $navigationSort = -1;
    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            AgentStatsWidget::class,
            AgentRecentInquiriesWidget::class,
            AgentListingsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return false;
        }

        // Only allow agents to access this dashboard
        return $user->role === 'agent';
    }
}
