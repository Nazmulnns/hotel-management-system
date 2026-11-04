<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\SupportRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stays = Booking::whereIn('status', ['checked_in', 'checked_out'])->with('rooms:id')->get();

        DB::transaction(function () use ($stays): void {
            foreach (range(1, 100) as $index) {
                $booking = $stays->random();

                SupportRequest::factory()->create([
                    'guest_id' => $booking->guest_id,
                    'room_id' => $booking->rooms->random()->id,
                ]);
            }
        });
    }
}
