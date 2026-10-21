<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\SupportRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportRequest>
 */
class SupportRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guest_id' => Guest::isRandomOrder()->value('id') ?? Guest::factory(),
            'issue' => fake()->randomElement([
                'AC not working',
                'Need extra towels',
                'Wi-Fi is slow',
                'Room cleaning request',
                'Hot water problem',
                'TV remote not working',
                'Noise from the next room',
                'Need an extra pillow',
                'Light bulb not working',
                'Door lock not working',
            ]),
            'status' => fake()->randomElement(['pending', 'in_progress', 'resolved']),
        ];
    }
}
