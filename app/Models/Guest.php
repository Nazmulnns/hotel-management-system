<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'nid_number',
        'date_of_birth',
        'nationality',
        'address',
        'nid_front_image',
        'nid_back_image',
        'guest_photo',
    ];

    protected $hidden = [
        'nid_number',
        'nid_front_image',
        'nid_back_image',
        'guest_photo',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function supportRequests(): HasMany
    {
        return $this->hasMany(SupportRequest::class);
    }

    public function loyaltyProgram(): HasOne
    {
        return $this->hasOne(LoyalityProgram::class)
            ->withDefault(['points' => 0]);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Booking::class);
    }

    public function loyaltyPoints(): int
    {
        return (int) $this->loyaltyProgram->points;
    }

    public function hasIdentity(): bool
    {
        return filled($this->nid_number)
            && filled($this->nid_front_image);
    }
}
