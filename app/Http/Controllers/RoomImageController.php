<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomImagesRequest;
use App\Http\Resources\RoomImageResource;
use App\Models\Room;
use App\Services\RoomImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomImageController extends Controller
{
    private RoomImageService $roomImageServie;
    public function __construct(RoomImageService $roomImageService)
    {
        $this->roomImageServie = $roomImageService;
    }

    public function addNewRoomImages(int $id, StoreRoomImagesRequest $request): AnonymousResourceCollection|JsonResponse
    {
        $room = Room::find($id);
        if (empty($room)) {
            return response()->json(["message" => "No room exists with id {$id}"]);
        }

        $images = $this->roomImageServie->addImages($room, $request->file('images'));
        return RoomImageResource::collection($images)
            ->additional(['message' => 'Images uploaded successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function deleteRoomImageById(int $id): JsonResponse
    {
        $image = $this->roomImageServie->findImageById($id);
        if (empty($image)) {
            return response()->json(["message" => "No image exists with id {$id}"]);
        }

        $this->roomImageServie->deleteImage($image);
        return response()->json(["message" => "Image deleted successfully"]);
    }
}
