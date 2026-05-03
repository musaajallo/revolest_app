<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuiltPropertyListingLead extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'in_review', 'contacted', 'converted', 'closed'];

    protected $fillable = [
        'first_name',
        'last_name',
        'nationality',
        'email',
        'phone',
        'street_address',
        'city',
        'region',
        'legal_description',
        'property_address',
        'land_dimension',
        'approximate_sqft',
        'property_status',
        'property_type',
        'buildings_on_property',
        'asking_price',
        'possession',
        'showing_instructions',
        'number_of_rooms',
        'bedrooms_detail',
        'bathrooms_detail',
        'age_of_house',
        'square_footage',
        'roof_type',
        'furnace',
        'amenities',
        'natural_features',
        'site_documents',
        'disclosures',
        'disclosures_other',
        'documents_attached',
        'referral_source',
        'referral_name',
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
        'asking_price' => 'decimal:2',
        'previous_company_contact' => 'boolean',
        'buildings_on_property' => 'array',
        'natural_features' => 'array',
        'site_documents' => 'array',
        'disclosures' => 'array',
        'documents_attached' => 'array',
        'details' => 'array',
        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function convertedProperty()
    {
        return $this->belongsTo(Property::class, 'converted_property_id');
    }
}
