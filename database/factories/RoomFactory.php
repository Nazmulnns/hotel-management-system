<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$floor, $roomNumber] = fake()->unique()->randomElement($this->possibleRooms());
        return [
            'floor' => $floor,
            'room_number' => $roomNumber,
            'room_type_id' => RoomType::factory(),
            'price_per_night' => fake()->randomFloat(2, 2000, 20000),
            'max_occupancy' => fake()->numberBetween(1, 8),
        ];
    }

    public function possibleRooms(): array
    {
        $rooms = [];

        foreach (range(1, 10) as $floor) {
            foreach (range('A', 'H') as $roomNumber) {
                $rooms[] = [$floor, $roomNumber];
            }
        }

        return $rooms;
    }
}
