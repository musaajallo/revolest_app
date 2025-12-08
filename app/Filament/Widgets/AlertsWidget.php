<?php

namespace App\Filament\Widgets;

use App\Models\RepairRequest;
use App\Models\Complaint;
use App\Models\Inquiry;
use Filament\Widgets\Widget;

class AlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.alerts-widget';
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 1;

    public function getViewData(): array
    {
        $openRepairs = RepairRequest::where('status', 'pending')
            ->orWhere('status', 'open')
            ->count();

        $openComplaints = Complaint::where('status', 'pending')
            ->orWhere('status', 'open')
            ->count();

        $pendingInquiries = Inquiry::where('status', 'pending')
            ->orWhere('status', 'new')
            ->count();

        return [
            'openRepairs' => $openRepairs,
            'openComplaints' => $openComplaints,
            'pendingInquiries' => $pendingInquiries,
        ];
    }
}
