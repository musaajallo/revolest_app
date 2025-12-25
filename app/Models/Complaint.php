<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'submitted_by_user_id',
        'property_id',
        'tenant_id',
        'description',
        'complaint_category',
        'priority',
        'status',
        'submitted_at',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Relationship to the user who submitted the complaint
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    // Property the complaint is about
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Tenant the complaint is about (nullable - may be property complaint)
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
