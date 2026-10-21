<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Revenue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Revenue>
 */
class RevenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'booking_id' => fn (array $attributes) => Payment::findOrFail($attributes['payment_id'])->booking_id,
            'source' => 'room_booking',
            'amount' => fn (array $attributes) => Payment::findOrFail($attributes['payment_id'])->amount,
            'revenue_date' => fn (array $attributes) => Payment::findOrFail($attributes['payment_id'])->payment_date,
        ];
    }
}
