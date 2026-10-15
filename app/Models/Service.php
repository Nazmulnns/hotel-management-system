<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'service_name',
        'price',
    ];

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class);
    }
}
