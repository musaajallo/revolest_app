<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'issued_at',
        'file',
        'amount',
        'description',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
