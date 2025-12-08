<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'unique_id',
        'name',
        'email',
        'phone',
        'bio',
        'photo',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Agent $agent) {
            if (empty($agent->unique_id)) {
                $lastId = static::withTrashed()->max('id') ?? 0;
                $agent->unique_id = 'AGT-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
