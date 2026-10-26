<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRoomsRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    private RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function getRooms(GetRoomsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $rooms = $this->roomService->getAllRooms($filters);

        return RoomResource::collection($rooms)->additional(['message' => "Rooms retrived successfully."]);
    }

    public function findRoomById(int $id): RoomResource|JsonResponse
    {
        $room = $this->roomService->findRoomById($id);
        if (empty($room)) {
            return response()->json(['message' => "No room exists with id {$id}"]);
        }

        return new RoomResource($room);
    }

    public function createRoom(StoreRoomRequest $request): RoomResource
    {
        $request = $request->validated();
        $room = $this->roomService->createNewRoom($request);

        return (new RoomResource($room))->additional(['message' => "New Room Added Successfully"]);
    }

    public function updateRoom(int $id, UpdateRoomRequest $request): RoomResource|JsonResponse
    {
        $request = $request->validated();
        $room = $this->roomService->findRoomById($id);
        if (empty($room)) {
            return response()->json(['message' => "No room exists with id {$id}"]);
        }

        $updatedRoom = $this->roomService->updateRoom($room, $request);

        return (new RoomResource($updatedRoom))->additional(["message" => "Room updated successfully"]);
    }

    public function deleteRoom(int $id): JsonResponse
    {
        $room = $this->roomService->findRoomById($id);
        if (empty($room)) {
            return response()->json(['message' => "No room exists with id $id"]);
        }

        $this->roomService->deleteRoomById($room);

        return response()->json(['message' => "Room with id {$id} is deleted successfully."]);
    }
}
