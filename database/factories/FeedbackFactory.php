<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->withRoom(),
            'guest_id' => fn (array $attributes) => Booking::findOrFail($attributes['booking_id'])->guest_id,
            'message' => fake()->sentence(),
        ];
    }
}
