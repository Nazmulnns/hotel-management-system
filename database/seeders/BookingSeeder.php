<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guests = Guest::all();
        $rooms = Room::all();

        DB::transaction(function () use ($guests, $rooms): void {
            foreach ($guests as $guest) {
                $numberOfBookings = fake()->randomElement([1,1,1,1,1,1,2,2,2,2,3,3,3]);
                for ($bookingNumber = 1; $bookingNumber <= $numberOfBookings; $bookingNumber++) {
                    $this->tryToBook($guest, $rooms);
                }
            }
        });
    }

    private function tryToBook(Guest $guest, $rooms): void
    {
        for ($attempt = 1; $attempt <= 30; $attempt++) {
            $room = $rooms->random();
            $checkIn = CarbonImmutable::today()->addDays(fake()->numberBetween(-60, 120));
            $checkOut = $checkIn->addDays(fake()->numberBetween(1, 7));

            if ($room->bookings()->overlapping($checkIn, $checkOut)->exists()) {
                continue;
            }

            $this->createBooking($guest, $room, $checkIn, $checkOut);
            return;
        }
    }

    private function createBooking(
        Guest $guest,
        Room $room,
        CarbonImmutable $checkIn,
        CarbonImmutable $checkOut
    ): Booking {

        $bookingFactory = Booking::factory()->for($guest);

        if (fake()->boolean(10)) {
            $bookingFactory = $bookingFactory->cancelled();
        }

        $booking = $bookingFactory->create([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);

        $booking->rooms()->attach($room->id, [
            'price_per_night' => $room->price_per_night,
            'guests' => fake()->numberBetween(1, $room->max_occupancy),
        ]);

        $nights = (int) $checkIn->diffInDays($checkOut);

        $booking->update(['total_amount' => round($room->price_per_night * $nights, 2)]);

        return $booking;
    }
}
