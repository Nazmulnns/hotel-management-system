<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomService
{
    public function getAllRooms(?array $filters): LengthAwarePaginator
    {
        $query = Room::with('images', 'maintenances', 'facilities', 'roomType');

        if (isset($filters['check_in'], $filters['check_out'])) {
            $query->whereDoesntHave('bookingRooms.booking', function ($q) use ($filters) {
                $q->where('check_in', '<', $filters['check_out'])->where('check_out', '>', $filters['check_out']);
            });
        }

        if (!empty($filters['room_type_ids'])) {
            $query->whereIn('room_type_id', $filters['room_type_ids']);
        }

        if (!empty($filters['facility_ids'])) {
            foreach ($filters['facility_ids'] as $facilityId) {
                $query->whereHas(
                    'facilities',
                    fn ($facilityQuery) => $facilityQuery->where(
                        'facilities.id',
                        $facilityId
                    )
                );
            }
        }

        if (isset($filters['min_price'])) {
            $query->where('price_per_night', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price_per_night', '<=', $filters['max_price']);
        }

        $sortBy = $filters['sort_by'] ?? 'price_per_night';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        $query->orderBy($sortBy, $sortOrder)
              ->orderBy('id', 'asc');

        return $query->paginate($filters['per_page'] ?? 10)->withQueryString();
    }

    public function findRoomById(int $id): ?Room
    {
        return Room::with([
            'roomType',
            'facilities',
            'images',
            'maintenances',
            'bookings' => fn ($query) => $query->upcoming(),
            'reviews' => fn ($query) => $query->with('guest:id,full_name')->latest()->limit(5),
        ])
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->find($id);
    }

    public function createNewRoom(array $data): Room
    {
        return DB::transaction(function () use ($data) {
            $room = Room::create($data);
            $room->facilities()->sync($data['facility_ids'] ?? []);
            return $room->load('roomType', 'facilities');
        });
    }

    public function updateRoom(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {
            $room->update($data);
            $room->facilities()->sync($data['facility_ids'] ?? []);
            return $room->load('roomType', 'facilities');
        });
    }

    public function deleteRoomById(Room $room): void
    {
        $hasActiveBookings = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->exists();

        if ($hasActiveBookings) {
            throw ValidationException::withMessages([
                'room' => "Room {$room->floor}{$room->room_number} has current or upcoming bookings and cannot be removed.",
            ]);
        }

        $room->delete();
    }
}