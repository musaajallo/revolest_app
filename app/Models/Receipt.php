<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'payment_id',
        'issued_at',
        'file',
        'amount',
        'description',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $receipt): void {
            if (blank($receipt->issued_at)) {
                $receipt->issued_at = now();
            }

            if (blank($receipt->receipt_number)) {
                $receipt->receipt_number = static::generateNumber($receipt->issued_at);
            }
        });
    }

    public static function generateNumber(?\DateTimeInterface $issuedAt = null): string
    {
        $year = ($issuedAt ?? now())->format('Y');

        $lastSeq = static::withTrashed()
            ->where('receipt_number', 'like', "RCV-{$year}-%")
            ->selectRaw("MAX(CAST(SUBSTRING(receipt_number, 10) AS UNSIGNED)) as seq")
            ->value('seq');

        $next = ((int) $lastSeq) + 1;

        return sprintf('RCV-%s-%06d', $year, $next);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
