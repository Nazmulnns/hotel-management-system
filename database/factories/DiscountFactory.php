<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discount_code' => fake()->words(2, true),
            'percentage' => fake()->numberBetween(5, 30),
            'expiry_date' => fake()->dateTimeBetween('now', '+6 months'),
        ];
    }
}
