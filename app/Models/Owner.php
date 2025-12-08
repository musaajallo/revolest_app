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
        'name',
        'email',
        'phone',
        'bio',
        'photo',
        'user_id',
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
