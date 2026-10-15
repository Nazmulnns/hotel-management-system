<?php

namespace App\Services;

use App\Models\RoomType;

class RoomTypeService
{
    public function getAllRoomTypes()
    {
        return RoomType::all();
    }

    public function isRoomTypeExists(string $roomTypeName): bool
    {
        return RoomType::where('name', $roomTypeName)->exists();
    }

    public function findRoomTypeById(int $roomTypeId): ?RoomType
    {
        return RoomType::find($roomTypeId);
    }

    public function updateRoomTypeById(RoomType $roomType, string $roomTypeName, ?string $roomTypeDescription = null): ?RoomType
    {
        $roomType->name = strtoupper(str_replace('"', '', $roomTypeName));
        $roomType->description = str_replace('"', '', $roomTypeDescription);
        $roomType->save();
        return $roomType;
    }

    public function addNewRoomType(string $roomTypeName, ?string $roomTypeDescription = null)
    {
        $newRoomType = new RoomType();
        $newRoomType->name = strtoupper(str_replace('"', '', $roomTypeName));
        $newRoomType->description = str_replace('"', '', $roomTypeDescription);
        $newRoomType->save();
        return $newRoomType;
    }

    public function deleteRoomType(RoomType $roomType): bool
    {
        if ($roomType->rooms()->exists()) {
            return false;
        }
        $roomType->rooms()->delete();
        return $roomType->delete();
    }
}
