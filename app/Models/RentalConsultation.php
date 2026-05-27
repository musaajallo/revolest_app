<?php

namespace App\Models;

use App\Traits\HasLeadActivities;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalConsultation extends Model
{
    use HasFactory, HasLeadActivities, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'in_review', 'contacted', 'converted', 'closed'];

    protected $fillable = [
        'consultation_date',
        'full_name',
        'address',
        'nationality',
        'occupation',
        'institution',
        'marital_status',
        'number_of_kids',
        'phone',
        'email',
        'preferred_locations',
        'property_kind',
        'bedrooms',
        'bathrooms',
        'furnished',
        'preferred_structure',
        'property_condition',
        'intended_use',
        'plot_size',
        'desired_facilities',
        'property_suggestions',
        'reason_for_moving',
        'occupants_count',
        'move_in_window',
        'rental_duration',
        'budget_min',
        'budget_max',
        'payment_plan',
        'payment_method',
        'payer',
        'payer_name',
        'payer_occupation',
        'payer_address',
        'payer_phone',
        'payer_relationship',
        'previous_company_contact',
        'previous_company_experience',
        'referral_source',
        'referral_name',
        'referred_by_name',
        'details',
        'notes',
        'status',
        'agent_id',
        'tenant_id',
        'signed_name',
        'signed_at',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'consultation_date' => 'date',
        'furnished' => 'boolean',
        'previous_company_contact' => 'boolean',
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

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
