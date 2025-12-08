<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inquiry extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'listing_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];

    // Relationships
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
