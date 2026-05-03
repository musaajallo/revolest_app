<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetApplication extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'in_review', 'approved', 'rejected', 'closed'];

    public const KEEP_LOCATIONS = ['indoors', 'outdoors', 'both'];

    protected $fillable = [
        'property_id',
        'tenant_id',
        'property_address',
        'tenant_name',
        'phone',
        'email',
        'lease_start_date',
        'pets',
        'keep_location',
        'supervised_outdoors',
        'past_complaints',
        'past_complaints_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
        'details',
        'notes',
        'status',
        'signed_name',
        'signed_at',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'pets' => 'array',
        'details' => 'array',
        'lease_start_date' => 'date',
        'supervised_outdoors' => 'boolean',
        'past_complaints' => 'boolean',
        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
