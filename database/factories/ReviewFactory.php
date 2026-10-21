<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'guest_id' => fn (array $attributes) => Booking::findOrFail($attributes['booking_id'])->guest_id,
            'rating' => fake()->numberBetween(1, 5),
            'text' => fake()->sentence(),
        ];
    }
}
