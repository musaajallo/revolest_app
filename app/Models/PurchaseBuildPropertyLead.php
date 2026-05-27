<?php

namespace App\Models;

use App\Traits\HasLeadActivities;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseBuildPropertyLead extends Model
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
        'property_type',
        'build_status',
        'property_condition',
        'intended_use',
        'preferred_location',
        'avoid_areas',
        'architectural_style',
        'bedrooms_bathrooms',
        'bedrooms',
        'bathrooms',
        'plot_size',
        'special_features',
        'luxury_features',
        'budget',
        'budget_min',
        'budget_max',
        'financing_method',
        'mortgage_preapproval',
        'needs_mortgage_advice',
        'open_to_negotiation',
        'min_square_footage',
        'needs_extra_space',
        'lot_size_preference',
        'storey_preference',
        'layout_preference',
        'proximity_preference',
        'area_kind',
        'amenities_importance',
        'community_type',
        'landmarks',
        'move_in_target',
        'time_sensitivity',
        'readiness_preference',
        'use_purpose',
        'long_term_value',
        'open_to_developments',
        'legal_requirements',
        'needs_inspection_help',
        'maintenance_effort',
        'maintenance_preference',
        'additional_services',
        'household_type',
        'accessibility_needs',
        'pet_accommodations',
        'eco_priority',
        'smart_home_interest',
        'customizable_required',
        'needs_reno_design_help',
        'resale_plan',
        'property_age_preference',
        'turnkey_preference',
        'other_considerations',
        'previous_company_contact',
        'previous_company_experience',
        'referral_source',
        'referral_name',
        'referred_by_name',
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
        'needs_mortgage_advice' => 'boolean',
        'open_to_negotiation' => 'boolean',
        'needs_extra_space' => 'boolean',
        'open_to_developments' => 'boolean',
        'needs_inspection_help' => 'boolean',
        'smart_home_interest' => 'boolean',
        'customizable_required' => 'boolean',
        'needs_reno_design_help' => 'boolean',
        'previous_company_contact' => 'boolean',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
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
