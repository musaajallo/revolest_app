<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const PURPOSES = [
        'rent' => 'Rent',
        'security_deposit' => 'Security Deposit',
        'agent_fee' => 'Agent Fee',
        'commission' => 'Commission',
        'late_fee' => 'Late Fee',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'complete' => 'Complete',
        'incomplete' => 'Incomplete',
        'failed' => 'Failed',
    ];

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'owner_id',
        'amount',
        'expected_amount',
        'commission_amount',
        'purpose',
        'period_start',
        'period_end',
        'period_label',
        'payment_date',
        'method',
        'paid_by_name',
        'received_by_user_id',
        'status',
        'receipt_file',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            if (blank($payment->purpose)) {
                $payment->purpose = 'rent';
            }

            if (blank($payment->commission_amount) && $payment->purpose === 'rent' && $payment->amount) {
                $lease = $payment->lease_id ? Lease::find($payment->lease_id) : null;
                $owner = $lease?->property?->owner ?? ($payment->owner_id ? Owner::find($payment->owner_id) : null);

                if ($owner) {
                    $rate = $owner->commissionRateFor($lease);
                    $payment->commission_amount = round(((float) $payment->amount) * $rate / 100, 2);
                }
            }
        });

        static::created(function (self $payment): void {
            if ($payment->status === 'complete') {
                $payment->ensureReceipt();

                if ($payment->purpose === 'rent' && $payment->lease) {
                    $payment->lease->advanceRentDue();
                }
            }
        });

        static::updated(function (self $payment): void {
            if ($payment->wasChanged('status') && $payment->status === 'complete') {
                $payment->ensureReceipt();

                if ($payment->purpose === 'rent' && $payment->lease) {
                    $payment->lease->advanceRentDue();
                }
            }
        });
    }

    public function ensureReceipt(): Receipt
    {
        if ($this->receipt) {
            return $this->receipt;
        }

        return Receipt::create([
            'payment_id' => $this->id,
            'issued_at' => $this->payment_date ?? now(),
            'amount' => $this->amount,
            'description' => static::PURPOSES[$this->purpose] ?? null,
        ]);
    }

    public function outstandingBalance(): ?float
    {
        if ($this->expected_amount === null) {
            return null;
        }

        return (float) $this->expected_amount - (float) $this->amount;
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
}
