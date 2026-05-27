<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Lease extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const RENT_CYCLES = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'annually' => 'Annually',
    ];

    public const DEPOSIT_STATUSES = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'partial' => 'Partial',
        'refunded' => 'Refunded',
        'forfeited' => 'Forfeited',
    ];

    protected $fillable = [
        'property_id',
        'tenant_id',
        'start_date',
        'end_date',
        'rent_amount',
        'status',
        'contract_file',
        'security_deposit_amount',
        'security_deposit_status',
        'rent_cycle',
        'next_rent_due_at',
        'commission_percent_override',
        'inspection_cycle_months',
        'last_inspection_at',
        'last_inspection_status',
        'next_inspection_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit_amount' => 'decimal:2',
        'commission_percent_override' => 'decimal:2',
        'inspection_cycle_months' => 'integer',
        'next_rent_due_at' => 'date',
        'last_inspection_at' => 'date',
        'next_inspection_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $lease): void {
            if (blank($lease->next_rent_due_at) && $lease->start_date) {
                $lease->next_rent_due_at = static::computeNextDueDate(
                    Carbon::parse($lease->start_date),
                    $lease->rent_cycle ?? 'annually'
                )->toDateString();
            }
        });
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function owner()
    {
        return $this->hasOneThrough(Owner::class, Property::class, 'id', 'id', 'property_id', 'owner_id');
    }

    public function daysUntilRentDue(): ?int
    {
        return $this->next_rent_due_at?->startOfDay()->diffInDays(now()->startOfDay(), false);
    }

    public function daysUntilNextInspection(): ?int
    {
        return $this->next_inspection_at?->startOfDay()->diffInDays(now()->startOfDay(), false);
    }

    public function advanceRentDue(): void
    {
        $from = $this->next_rent_due_at ?? ($this->start_date ? Carbon::parse($this->start_date) : now());
        $this->next_rent_due_at = static::computeNextDueDate(Carbon::parse($from), $this->rent_cycle ?? 'annually')->toDateString();
        $this->saveQuietly();
    }

    public static function computeNextDueDate(Carbon $from, string $cycle): Carbon
    {
        return match ($cycle) {
            'monthly' => $from->copy()->addMonth(),
            'quarterly' => $from->copy()->addMonths(3),
            default => $from->copy()->addYear(),
        };
    }
}
