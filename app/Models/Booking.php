<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'guest_id', 'booking_date', 'check_in', 'check_out', 'total_amount', 'status'];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'check_in' => 'date',
            'check_out' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'booking_rooms')
            ->using(BookingRoom::class)
            ->withPivot(['id', 'price_per_night', 'guests'])
            ->withTimestamps();
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_orders')
            ->withPivot(['id', 'quantity', 'total_price'])
            ->withTimestamps();
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'applied_discounts')
            ->withoutGlobalScopes()
            ->withPivot('amount_saved')
            ->withTimestamps();
    }

    public function scopeOverlapping(Builder $query, CarbonInterface $checkIn, CarbonInterface $checkOut): void
    {
        $query->where($query->qualifyColumn('check_in'), '<=', $checkOut)
            ->where($query->qualifyColumn('check_out'), '>=', $checkIn)
            ->where($query->qualifyColumn('status'), '!=', 'cancelled');
    }
}
