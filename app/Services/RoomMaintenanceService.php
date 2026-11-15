<?php

namespace App\Services;

use App\Enums\MaintenanceStatus;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Notifications\MaintenanceReported;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification;

class RoomMaintenanceService
{
    public function getMaintenances(array $filters): LengthAwarePaginator
    {
        $query = RoomMaintenance::query()->with('room');

        if (isset($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                $query->whereLike('issue_description', "%{$filters['search']}%")
                    ->orWhereLike('maintenance_status', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->whereIn('maintenance_status', $filters['status']);
        }

        if (isset($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (isset($filters['floor'])) {
            $query->whereRelation('room', 'floor', $filters['floor']);
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

    public function findMaintenanceById(int $id): ?RoomMaintenance
    {
        return RoomMaintenance::with('room')->find($id);
    }

    public function reportMaintenance(Room $room, array $data): RoomMaintenance
    {
        $maintenance = $room->maintenances()->create([
            'issue_description' => $data['issue_description'],
            'maintenance_status' => MaintenanceStatus::Pending,
        ]);

        Notification::route('mail', config('mail.manager_address'))
        ->notify(new MaintenanceReported($maintenance));

        return $maintenance;
    }

    public function updateMaintenance(RoomMaintenance $maintenance, array $data): RoomMaintenance
    {
        $maintenance->update($data);

        return $maintenance->load('room');
    }
}
