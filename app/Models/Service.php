<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'service_name',
        'price',
    ];

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'service_orders')
            ->withPivot(['quantity', 'total_price'])
            ->withTimestamps();
    }
}
