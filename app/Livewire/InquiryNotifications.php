<?php

namespace App\Livewire;

use App\Models\Inquiry;
use Livewire\Component;

class InquiryNotifications extends Component
{
    public function markAsRead($inquiryId)
    {
        $inquiry = Inquiry::find($inquiryId);
        if ($inquiry && $inquiry->status === 'new') {
            $inquiry->update(['status' => 'read']);
        }
    }

    public function markAllAsRead()
    {
        Inquiry::where('status', 'new')->update(['status' => 'read']);
    }

    public function getNewInquiriesCountProperty()
    {
        return Inquiry::where('status', 'new')->count();
    }

    public function getRecentInquiriesProperty()
    {
        return Inquiry::where('status', 'new')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.inquiry-notifications');
    }
}
