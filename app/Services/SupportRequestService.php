<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\SupportRequestStatus;
use App\Models\Guest;
use App\Models\SupportRequest;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class SupportRequestService
{
    public function getSupportRequests(array $filters): LengthAwarePaginator
    {
        $query = SupportRequest::query()->with(['guest', 'room']);

        if (isset($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query->whereLike('issue', "%{$search}%")
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
            $query->where('room_id', $filters['room_id']);
        }

        if (isset($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        return $query
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function findSupportRequestById(int $id): ?SupportRequest
    {
        return SupportRequest::with(['guest', 'room'])->find($id);
    }

    public function createSupportRequest(Guest $guest, array $data): SupportRequest
    {
        $roomIds = $this->roomIdsOnDate($guest, today());

        if (isset($data['room_id']) && ! in_array((int) $data['room_id'], $roomIds, true)) {
            throw ValidationException::withMessages([
                'room_id' => 'This guest is not currently staying in the selected room.',
            ]);
        }

        $roomId = $data['room_id'] ?? (count($roomIds) === 1 ? $roomIds[0] : null);

        return $guest->supportRequests()->create([
            'room_id' => $roomId,
            'issue' => $data['issue'],
            'status' => SupportRequestStatus::Pending,
        ])->load(['guest', 'room']);
    }

    public function updateSupportRequest(SupportRequest $supportRequest, array $data): SupportRequest
    {
        if ($supportRequest->status === SupportRequestStatus::Resolved) {
            throw ValidationException::withMessages([
                'support_request' => 'A resolved support request cannot be changed.',
            ]);
        }

        if (isset($data['room_id'])) {
            $roomIds = $this->roomIdsOnDate($supportRequest->guest, $supportRequest->created_at);

            if (! in_array((int) $data['room_id'], $roomIds, true)) {
                throw ValidationException::withMessages([
                    'room_id' => 'The guest was not staying in the selected room when this request was made.',
                ]);
            }
        }

        if (isset($data['status'])) {
            $newStatus = SupportRequestStatus::from($data['status']);

            if ($newStatus !== $supportRequest->status) {
                throw ValidationException::withMessages([
                    'status' => "Cannot change status from {$supportRequest->status->label()} to {$newStatus->label()}.",
                ]);
            }
        }

        $supportRequest->update($data);

        return $supportRequest->load(['guest', 'room']);
    }

    /**
     * @return array<int, int>
     */
    private function roomIdsOnDate(Guest $guest, CarbonInterface $date): array
    {
        return $guest->bookings()
            ->whereNotIn('status', [BookingStatus::Cancelled, BookingStatus::Pending])
            ->whereDate('check_in', '<=', $date)
            ->whereDate('check_out', '>=', $date)
            ->with('rooms:id')
            ->get()
            ->pluck('rooms')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }
}
