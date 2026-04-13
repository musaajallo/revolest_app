<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'address',
        'plus_code',
        'price',
        'sale_price',
        'rental_price',
        'security_deposit',
        'agent_fee',
        'type',
        'purpose',
        'listing_category',
        'status',
        'is_featured',
        'available_from',
        'is_storey_building',
        'number_of_storeys',
        'bedrooms',
        'bathrooms',
        'area',
        'images',
        'owner_id',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'rental_price' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'agent_fee' => 'decimal:2',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_storey_building' => 'boolean',
        'available_from' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Property $property): void {
            if ($property->purpose === 'sale') {
                $property->sale_price = $property->sale_price ?? $property->price;
                $property->rental_price = null;
                $property->price = $property->sale_price;
            } elseif ($property->purpose === 'rent') {
                $property->rental_price = $property->rental_price ?? $property->price;
                $property->sale_price = null;
                $property->price = $property->rental_price;
            } else {
                if (blank($property->price)) {
                    $property->price = $property->sale_price ?? $property->rental_price ?? 0;
                }
            }
        });

        static::creating(function (Property $property): void {
            if (blank($property->purpose)) {
                $property->purpose = 'mixed';
            }

            if (blank($property->status)) {
                $property->status = 'inactive';
            }

            if (blank($property->listing_category)) {
                $property->listing_category = $property->type === 'land' ? 'land' : 'building';
            }
        });
    }

    // Relationships
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function usesMixedUnitPricing(): bool
    {
        return $this->purpose === 'mixed';
    }

    public function publicPrice(?Listing $listing = null): ?float
    {
        if ($this->purpose === 'sale') {
            return (float) ($this->sale_price ?? $this->price ?? 0);
        }

        if ($this->purpose === 'rent') {
            return (float) ($this->rental_price ?? $this->price ?? 0);
        }

        return $listing ? (float) ($listing->price ?? 0) : (float) ($this->sale_price ?? $this->rental_price ?? $this->price ?? 0);
    }

    public function publicDeposit(?Listing $listing = null): ?float
    {
        if ($this->purpose === 'rent') {
            return $this->security_deposit !== null ? (float) $this->security_deposit : null;
        }

        if ($this->purpose === 'mixed' && $listing?->status === 'for_rent') {
            return $listing->security_deposit !== null ? (float) $listing->security_deposit : null;
        }

        return null;
    }

    public function publicAgentFee(?Listing $listing = null): ?float
    {
        if ($this->purpose === 'rent') {
            return $this->agent_fee !== null ? (float) $this->agent_fee : null;
        }

        if ($this->purpose === 'mixed' && $listing?->status === 'for_rent') {
            return $listing->agent_fee !== null ? (float) $listing->agent_fee : null;
        }

        return null;
    }

    public function publicPriceLabel(?Listing $listing = null): string
    {
        if ($this->purpose === 'sale') {
            return 'Sale Price';
        }

        if ($this->purpose === 'rent') {
            return 'Yearly Rent';
        }

        if ($listing?->status === 'for_rent') {
            return 'Rental Price';
        }

        return 'Sale Price';
    }

    public function getUnitsCountAttribute(): int
    {
        return $this->listings()->count();
    }

    public function syncStatusFromUnits(): void
    {
        $this->status = $this->listings()->exists() ? 'active' : 'inactive';
        $this->saveQuietly();
    }

    public function marketListings()
    {
        return $this->hasMany(Listing::class)->whereIn('status', Listing::PUBLIC_STATUSES);
    }

    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
}
