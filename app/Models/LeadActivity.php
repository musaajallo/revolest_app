<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    use HasFactory;

    public const KINDS = [
        'note' => 'Note',
        'status_change' => 'Status Change',
        'contact_attempt' => 'Contact Attempt',
        'meeting' => 'Meeting',
        'viewing' => 'Viewing',
    ];

    protected $fillable = [
        'subject_type',
        'subject_id',
        'user_id',
        'kind',
        'body',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
