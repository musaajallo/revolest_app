<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerFeedback extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'reviewed', 'archived'];

    protected $table = 'customer_feedbacks';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'overall_satisfaction',
        'service_quality',
        'customer_service_experience',
        'staff_helpful',
        'delivery_on_time',
        'ease_of_finding',
        'would_recommend',
        'accessibility_score',
        'expectations_met',
        'brand_score',
        'heard_about_us',
        'heard_about_us_other',
        'improvement_suggestions',
        'additional_comments',
        'why_chose_us',
        'missing_features',
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
        'details' => 'array',
        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];
}
