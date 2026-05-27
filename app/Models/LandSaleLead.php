<?php

namespace App\Models;

use App\Traits\HasLeadActivities;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandSaleLead extends Model
{
    use HasFactory, HasLeadActivities, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'in_review', 'contacted', 'converted', 'closed'];

    protected $fillable = [
        'full_name',
        'email',
        'phone_primary',
        'phone_secondary',
        'phone_tertiary',
        'current_address',
        'land_location',
        'land_size',
        'current_use',
        'current_use_other',
        'jointly_owned',
        'ownership_disputes',
        'zoning',
        'asking_price',
        'consultation_purpose',
        'consultation_purpose_other',
        'plans_for_land',
        'current_issues',
        'has_liens',
        'taxes_up_to_date',
        'has_legal_documents',
        'documents_provided',
        'free_from_disputes',
        'utilities',
        'road_accessible',
        'existing_structures',
        'environmental_concerns',
        'has_recent_survey',
        'land_history',
        'budget_min',
        'budget_max',
        'bathrooms',
        'property_condition',
        'intended_use',
        'referred_by_name',
        'referral_source',
        'referral_notes',
        'previous_company_contact',
        'previous_company_experience',
        'details',
        'notes',
        'status',
        'agent_id',
        'converted_property_id',
        'signed_name',
        'signed_at',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'jointly_owned' => 'boolean',
        'ownership_disputes' => 'boolean',
        'has_liens' => 'boolean',
        'taxes_up_to_date' => 'boolean',
        'has_legal_documents' => 'boolean',
        'free_from_disputes' => 'boolean',
        'road_accessible' => 'boolean',
        'has_recent_survey' => 'boolean',
        'previous_company_contact' => 'boolean',
        'asking_price' => 'decimal:2',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'bathrooms' => 'integer',
        'consultation_purpose' => 'array',
        'documents_provided' => 'array',
        'utilities' => 'array',
        'details' => 'array',
        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function convertedProperty()
    {
        return $this->belongsTo(Property::class, 'converted_property_id');
    }
}
