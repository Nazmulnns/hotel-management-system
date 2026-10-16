<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRoomsRequest;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function getRooms(GetRoomsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $rooms = $this->roomService->getAllRooms($filters);

        return response() -> json([
            'message' => "Rooms retrieved succesfully",
            'data' => $rooms->items(),
            'pagination' => [
                'current_page' => $rooms->currentPage(),
                'per_page' => $rooms->perPage(),
                'total' => $rooms->total(),
                'last_page' => $rooms->lastPage(),
                'has_more_pages' => $rooms->hasMorePages(),
            ],
        ]);
    }
}
