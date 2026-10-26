<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RoomImageService
{

    public function addImages(Room $room, array $images): Collection
    {
        $paths = [];

        try {
            foreach ($images as $image) {
                $paths[] = $image->store("rooms/{$room->id}", 'public');
            }

            return $room->images()->createMany(array_map(fn ($path) => ['image_url' => $path], $paths));
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($paths);

            throw $exception;
        }
    }

    public function findImageById(int $id): ?RoomImage
    {
        return RoomImage::find($id);
    }
    public function deleteImage(RoomImage $image): void
    {

        $image->delete();

        Storage::disk('public')->delete($image->image_url);
    }
}
