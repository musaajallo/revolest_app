<?php

namespace App\Traits;

use App\Models\LeadActivity;

trait HasLeadActivities
{
    public function activities()
    {
        return $this->morphMany(LeadActivity::class, 'subject')->latest();
    }
}
