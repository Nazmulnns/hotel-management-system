<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomTypeRequest;
use App\Services\RoomTypeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    private RoomTypeService $roomTypeService;

    public function __construct(RoomTypeService $roomTypeService)
    {
        $this->roomTypeService = $roomTypeService;
    }

    public function getAllRoomTypes(): JsonResponse
    {
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        return response()->json(['data' => $roomTypes], 200);
    }

    public function updateRoomType(StoreRoomTypeRequest $request): JsonResponse
    {
        try {
            $request->validated();
            $roomTypeId = $request->id;
            $roomType = $this->roomTypeService->findRoomTypeById($roomTypeId);

            if (empty($roomType)) {
                return response()->json(['message' => "Room Type does not exists with roomType id {$roomTypeId}"], 404);
            }

            $this->roomTypeService->updateRoomTypeById($roomType, $request->name, $request->description);
            return response()->json(['message' => "Room Type Updated Successfully"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function addNewRoomType(StoreRoomTypeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $newRoomTypeName = $validated['name'];
            if ($this->roomTypeService->isRoomTypeExists($newRoomTypeName)) {
                return response()->json(['message' => "Room Type {$newRoomTypeName} is already exists"], 409);
            }

            $this->roomTypeService->addNewRoomType($newRoomTypeName, $request->description);
            return response()->json(['message' => "Room Type {$newRoomTypeName} is successfully added"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function deleteRoomTypeById(int $id): JsonResponse
    {
        try {
            $roomType = $this->roomTypeService->findRoomTypeById($id);
            if (empty($roomType)) {
                return response()->json(['message' => "RoomType does not exists with roomType id {$id}"], 404);
            }

            $isDeleted = $this->roomTypeService->deleteRoomType($roomType);
            if (!$isDeleted) {
                return response()->json([
                    'message' => "Cannot delete '{$roomType->name}' because rooms are assigned to this room type.
                    Please change the room type of those rooms before deleting it."
                ], 409);
            }
            return response()->json(['message' => "Room Type {$roomType->name} is deleted successfully"], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong {$e}"], 500);
        }
    }
}
