<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetMaintenanceStatusRequest;
use App\Http\Requests\GetRoomMaintenancesRequest;
use App\Http\Requests\StoreRoomMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceStatusRequest;
use App\Http\Requests\UpdateRoomMaintenanceRequest;
use App\Http\Resources\RoomMaintenanceResource;
use App\Models\Room;
use App\Services\RoomMaintenanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomMaintenanceController extends Controller
{
    public function __construct(private RoomMaintenanceService $roomMaintenanceService) {}

    public function getMaintenances(GetMaintenanceStatusRequest $request): AnonymousResourceCollection
    {
        return RoomMaintenanceResource::collection($this->roomMaintenanceService->getMaintenances($request->validated()));
    }

    public function getRoomMaintenances(GetMaintenanceStatusRequest $request, int $id): AnonymousResourceCollection|JsonResponse
    {
        if (! Room::whereKey($id)->exists()) {
            return response()->json(['message' => "No room found with id {$id}"], 404);
        }

        $filters = [...$request->validated(), 'room_id' => $id];

        return RoomMaintenanceResource::collection($this->roomMaintenanceService->getMaintenances($filters));
    }

    public function findMaintenanceById(int $id): RoomMaintenanceResource|JsonResponse
    {
        $maintenance = $this->roomMaintenanceService->findMaintenanceById($id);

        if (empty($maintenance)) {
            return response()->json(['message' => "No maintenance record found with id {$id}"], 404);
        }

        return new RoomMaintenanceResource($maintenance);
    }

    public function reportMaintenance(StoreRoomMaintenanceRequest $request, int $id): RoomMaintenanceResource|JsonResponse
    {
        $room = Room::find($id);

        if (empty($room)) {
            return response()->json(['message' => "No room found with id {$id}"], 404);
        }

        return (new RoomMaintenanceResource($this->roomMaintenanceService->reportMaintenance($room, $request->validated())))
            ->additional(['message' => 'Maintenance issue reported successfully.']);
    }

    public function updateMaintenance(UpdateMaintenanceStatusRequest $request, int $id): RoomMaintenanceResource|JsonResponse
    {
        $maintenance = $this->roomMaintenanceService->findMaintenanceById($id);

        if (empty($maintenance)) {
            return response()->json(['message' => "No maintenance record found with id {$id}"], 404);
        }

        return (new RoomMaintenanceResource($this->roomMaintenanceService->updateMaintenance($maintenance, $request->validated())))
            ->additional(['message' => 'Maintenance record updated successfully.']);
    }
}