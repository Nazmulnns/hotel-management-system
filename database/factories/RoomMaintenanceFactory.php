<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomMaintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomMaintenance>
 */
class RoomMaintenanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'issue_description' => fake()->randomElement(['AC servicing', 'Plumbing check', 'Repainting', 'Light replacement', 'Carpet cleaning']),
            'maintenance_status' => fake()->randomElement(['pending', 'inProgress', 'completed']),
        ];
    }
}
