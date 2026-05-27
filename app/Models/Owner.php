<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'unique_id',
        'user_id',
        'name',
        'email',
        'phone',
        'bio',
        'photo',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_branch',
        'commission_percent',
    ];

    protected $casts = [
        'commission_percent' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commissionRateFor(?Lease $lease = null): float
    {
        if ($lease && $lease->commission_percent_override !== null) {
            return (float) $lease->commission_percent_override;
        }

        return (float) ($this->commission_percent ?? 0);
    }

    protected static function booted(): void
    {
        static::creating(function (Owner $owner) {
            if (empty($owner->unique_id)) {
                $lastId = static::withTrashed()->max('id') ?? 0;
                $owner->unique_id = 'OWN-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

}
