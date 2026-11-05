<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Mail\ReviewRequest;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mail;
use function Illuminate\Support\minutes;

class BookingService
{
    public function getBookings(?array $filters): LengthAwarePaginator
    {
        $query = Booking::query()->with(['guest', 'rooms.roomType']);
       
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->whereLike('reference', "%{$search}%")
                    ->orWhereRelation('guest', 'full_name', 'like', "%{$search}%")
                    ->orWhereRelation('guest', 'phone_number', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        if (isset($filters['guest_id'])) {
            $query->where('guest_id', $filters['guest_id']);
        }

        if (isset($filters['room_id'])) {
            $query->whereRelation('rooms', 'rooms.id', $filters['room_id']);
        }

        if (isset($filters['check_in_from'])) {
            $query->whereDate('check_in', '>=', $filters['check_in_from']);
        }

        if (isset($filters['check_in_to'])) {
            $query->whereDate('check_in', '<=', $filters['check_in_to']);
        }

        if (isset($filters['staying_on'])) {
            $query->whereDate('check_in', '<=', $filters['staying_on'])
            ->whereDate('check_out', '>', $filters['staying_on'])
            ->whereNotIn('status', [BookingStatus::Cancelled, BookingStatus::CheckedOut]);;
        }

        return $query
                ->orderBy($filters['sort_by'] ?? 'check_in', $filters['sort_order'] ?? 'desc')
                ->orderBy('id')
                ->paginate($filters['per_page'] ?? 10)
                ->withQueryString();
    }

    public function findBookingById(int $id): ?Booking
    {
        return Booking::with(['guest', 'rooms.roomType'])->find($id);
    }
    public function updateStatus(Booking $booking, BookingStatus $newStatus): Booking
    {
        if ($newStatus === BookingStatus::CheckedIn && $booking->check_in->isFuture()) {
            throw ValidationException::withMessages([
                'status' => "This booking cannot be checked in before {$booking->check_in->toDateString()}.",
            ]);
        }

        if ($newStatus === BookingStatus::CheckedOut) {
            $this->sendReviewRequest($booking);
        }

        $booking->update(['status' => $newStatus]);

        return $booking->load(['guest', 'rooms.roomType']);
    }

    public function createBooking(array $data): Booking
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $requestedRooms = collect($data['rooms'])->keyBy('room_id');

        return DB::transaction(function () use ($data, $checkIn, $checkOut, $requestedRooms) {
            $rooms = Room::whereIn('id', $requestedRooms->keys())->lockForUpdate()->get()->keyBy('id');

            $this->ensureRoomsAreBookable($rooms, $requestedRooms, $checkIn, $checkOut);

            $nights = (int) $checkIn->diffInDays($checkOut);
            $totalAmount = $rooms->sum(fn (Room $room) => $room->price_per_night * $nights);

            $booking = Booking::create([
                'reference' => $this->generateReference(),
                'guest_id' => $data['guest_id'],
                'booking_date' => today(),
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'total_amount' => $totalAmount,
                'status' => BookingStatus::Pending,
            ]);

            $booking->rooms()->attach(
                $rooms->mapWithKeys(fn (Room $room) => [
                    $room->id => [
                        'price_per_night' => $room->price_per_night,
                        'guests' => $requestedRooms[$room->id]['guests'],
                    ],
                ])->all()
            );

            return $booking->load(['guest', 'rooms.roomType']);
        });
    }

    /**
     * @param  Collection<int, Room>  $rooms
     * @param  \Illuminate\Support\Collection<int, array{room_id: int, guests: int}>  $requestedRooms
     */
    private function ensureRoomsAreBookable(Collection $rooms, \Illuminate\Support\Collection $requestedRooms, Carbon $checkIn, Carbon $checkOut): void
    {
        foreach ($rooms as $room) {
            $label = "{$room->floor}{$room->room_number}";

            if ($requestedRooms[$room->id]['guests'] > $room->max_occupancy) {
                throw ValidationException::withMessages([
                    'rooms' => "Room {$label} allows at most {$room->max_occupancy} guests.",
                ]);
            }

            if ($room->maintenances()->whereIn('maintenance_status', [MaintenanceStatus::Pending, MaintenanceStatus::InProgress])->exists()) {
                throw ValidationException::withMessages([
                    'rooms' => "Room {$label} is under maintenance.",
                ]);
            }

            if ($room->bookings()->overlapping($checkIn, $checkOut)->exists()) {
                throw ValidationException::withMessages([
                    'rooms' => "Room {$label} is already booked for these dates.",
                ]);
            }
        }
    }

    private function generateReference(): string
    {
        do {
            $reference = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        } while (Booking::where('reference', $reference)->exists());

        return $reference;
    }

    private function sendReviewRequest(Booking $booking): void
    {
       /* if ($booking->reviews()->exists()) {
            return;
        }*/

        Mail::to($booking->guest->email)->later(
            now()->plus(minutes: 1),
            (new ReviewRequest($booking))->afterCommit(),
        );
    }
}
