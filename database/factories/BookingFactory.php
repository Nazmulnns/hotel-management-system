<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
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
            'booking_date' => fn(array $attributes) => CarbonImmutable::parse($attributes['check_in'])->subDays(fake()->numberBetween(1, 30))->min(CarbonImmutable::today()),
            'total_amount' => 0,
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

    public function withRoom(?Room $room = null): static
    {
        return $this->afterCreating(function (Booking $booking) use ($room): void {
            $room ??= Room::factory()->create();

            $booking->rooms()->attach($room->id, [
                'price_per_night' => $room->price_per_night,
                'guests' => fake()->numberBetween(1, $room->max_occupancy),
            ]);

            $nights = (int) CarbonImmutable::parse($booking->check_in)->diffInDays($booking->check_out);

            $booking->update([
                'total_amount' => round($room->price_per_night * $nights, 2),
            ]);
        });
    }
}
