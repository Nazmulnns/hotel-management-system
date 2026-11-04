<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function getReviews(array $filters): LengthAwarePaginator
    {
        $query = Review::query()->with('guest:id,full_name');

        if (isset($filters['room_id'])) {
            $query->whereRelation('booking.rooms', 'rooms.id', $filters['room_id']);
        }

        if (isset($filters['guest_id'])) {
            $query->where('guest_id', $filters['guest_id']);
        }

        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        return $query
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function addReview(Booking $booking, array $data): Review
    {
        if ($booking->status !== BookingStatus::CheckedOut) {
            throw ValidationException::withMessages([
                'booking' => 'A review can only be added after the guest has checked out.',
            ]);
        }

        if ($booking->reviews()->exists()) {
            throw ValidationException::withMessages([
                'booking' => 'This booking already has a review.',
            ]);
        }

        return $booking->reviews()->create([
            'guest_id' => $booking->guest_id,
            'rating' => $data['rating'],
            'text' => $data['text'],
        ])->load('guest:id,full_name');
    }
}
