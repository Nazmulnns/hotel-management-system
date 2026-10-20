<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Guest;
use Attribute;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->sentence(),
            'guest_id' => Guest::factory(),
            'check_in' => CarbonImmutable::parse(fake()->dateTimeBetween('-2 months', '+2 months'))->startOfDay(),
            'check_out' => fn(array $attributes) => CarbonImmutable::parse(fake()->dateTimeBetween(
                CarbonImmutable::parse($attributes['check_in'])->addDay(),
                CarbonImmutable::parse($attributes['check_in'])->addDays(7)
            ))->startOfDay(),
            'total_amount' => fake()->randomFloat(2, 1000, 20000),
            'status' => fn (array $attributes) => $this->statusForDates(
                CarbonImmutable::parse($attributes['check_in']),
                CarbonImmutable::parse($attributes['check_out']),
            ),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) =>[
            'status' => 'cancelled',
        ]);
    }

    private function statusForDates(CarbonImmutable $checkIn, CarbonImmutable $checkOut): string
    {
         $today = CarbonImmutable::today();

         return match (true) {
            $checkOut->lte($today) => 'checked_out',
            $checkIn->lte($today) => 'checked_in',
            default => fake()->randomElement(['pending', 'confirmed']),
         };
    }
}
