<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandPurchaseLead extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'in_review', 'contacted', 'converted', 'closed'];

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
        'identification_type',
        'identification_number',
        'id_attached',
        'preferred_locations',
        'plot_size',
        'with_buildings',
        'future_development',
        'land_type',
        'budget',
        'payment_plan',
        'payment_method',
        'timeframe',
        'completion_target',
        'details',
        'notes',
        'special_requirements',
        'status',
        'agent_id',
        'signed_name',
        'signed_at',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'id_attached' => 'boolean',
        'future_development' => 'boolean',
        'details' => 'array',
        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
