<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepairRequest extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'in_progress', 'awaiting_parts', 'completed', 'cancelled'];

    public const PRIORITIES = ['urgent', 'immediate', 'emergency'];

    public const PREFERRED_VISITS = ['home', 'anytime', 'call_to_confirm', 'fix_appointment'];

    protected $fillable = [
        'property_id',
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'property_address',
        'apartment_number',
        'description',
        'priority',
        'category',
        'preferred_visit',
        'has_pets',
        'pet_notes',
        'permission_to_enter',
        'tenant_signature_name',
        'signed_at',
        'ip_address',
        'user_agent',
        'status',
        'submitted_at',
        'resolved_at',
        'completed_at',
        'completed_by_name',
        'completion_notes',
    ];

    protected $casts = [
        'has_pets' => 'boolean',
        'permission_to_enter' => 'boolean',
        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getFullNameAttribute(): ?string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);

        return $parts ? implode(' ', $parts) : ($this->tenant?->name);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
