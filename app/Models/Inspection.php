<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const STATUSES = ['pass', 'issues_found', 'fail', 'pending_followup'];

    protected $fillable = [
        'lease_id',
        'property_id',
        'inspector_user_id',
        'inspected_at',
        'status',
        'findings',
        'next_inspection_due_at',
        'images',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'next_inspection_due_at' => 'date',
        'images' => 'array',
    ];

    protected static function booted(): void
    {
        $syncLeaseCache = function (self $inspection): void {
            $lease = Lease::find($inspection->lease_id);
            if (! $lease) {
                return;
            }

            $latest = static::where('lease_id', $lease->id)
                ->orderByDesc('inspected_at')
                ->first();

            if ($latest) {
                $lease->last_inspection_at = $latest->inspected_at?->toDateString();
                $lease->last_inspection_status = $latest->status;
                $lease->next_inspection_at = $latest->next_inspection_due_at?->toDateString()
                    ?? optional($latest->inspected_at)->copy()
                        ->addMonths((int) ($lease->inspection_cycle_months ?? 6))
                        ?->toDateString();
            } else {
                $lease->last_inspection_at = null;
                $lease->last_inspection_status = null;
                $lease->next_inspection_at = null;
            }

            $lease->saveQuietly();
        };

        static::created($syncLeaseCache);
        static::updated($syncLeaseCache);
        static::deleted($syncLeaseCache);
        static::restored($syncLeaseCache);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }
}
