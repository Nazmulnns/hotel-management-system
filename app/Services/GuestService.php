<?php

namespace App\Services;

use App\Models\Guest;
use Illuminate\Pagination\LengthAwarePaginator;
use Storage;

class GuestService
{
    public function findGuestById(int $id): ?Guest
    {
        return Guest::with('bookings')->find($id);
    }

    public function getGuests(?array $filters): LengthAwarePaginator
    {
        $query = Guest::with('bookings');
        if (isset($filters['search'])) {
            $query->whereAny(['email', 'full_name', 'phone_number'], 'like', "%{$filters['search']}%");
        }

        if (isset($filters['identity_number'])) {
            $query->where('identity_hash', Guest::hashIdentity($filters['identity_number']));
        }

        $sortBy = $filters['sort_by'] ?? 'updated_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);
        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage)->withQueryString();
    }

    public function createGuest(array $data): Guest
    {
        $data['identity_front_image'] = $data['identity_front_image']->store('guests', 'local');
        if (isset($data['identity_back_image'])) {
            $data['identity_back_image'] = $data['identity_back_image']->store('guests', 'local');
        }
        $data['guest_photo'] = $data['guest_photo']->store('guests', 'public');

        return Guest::create($data);
    }
}
