<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Listing extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'property_id',
        'agent_id',
        'unit_name',
        'floor_number',
        'price',
        'security_deposit',
        'agent_fee',
        'status',
        'bedrooms',
        'bathrooms',
        'area',
        'guest_toilets',
        'has_dining_area',
        'boys_quarters',
        'kitchens',
        'has_guest_toilet',
        'amenities',
        'description',
        'listed_by_company',
        'images',
        'published_at',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'listed_by_company' => 'boolean',
        'has_dining_area' => 'boolean',
        'has_guest_toilet' => 'boolean',
        'guest_toilets' => 'integer',
        'floor_number' => 'integer',
        'published_at' => 'datetime',
    ];

    public const PUBLIC_STATUSES = ['for_rent', 'for_sale'];

    protected static function booted(): void
    {
        static::creating(function (self $listing): void {
            if (blank($listing->published_at)) {
                $listing->published_at = Carbon::now();
            }

            if (blank($listing->price)) {
                $listing->price = 0;
            }

            if (blank($listing->status)) {
                $property = $listing->relationLoaded('property') ? $listing->property : Property::withTrashed()->find($listing->property_id);

                $listing->status = match ($property?->purpose) {
                    'sale' => 'for_sale',
                    'rent' => 'for_rent',
                    default => 'for_rent',
                };
            }
        });

        $syncPropertyStatus = function (self $listing): void {
            $property = Property::withTrashed()->find($listing->property_id);

            if ($property) {
                $property->syncStatusFromUnits();
            }
        };

        static::created($syncPropertyStatus);
        static::updated($syncPropertyStatus);
        static::deleted($syncPropertyStatus);
        static::restored($syncPropertyStatus);
        static::forceDeleted($syncPropertyStatus);
    }

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }
}
