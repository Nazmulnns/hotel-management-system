<?php

namespace Database\Seeders;

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
        $guestsWhoStayed = Guest::whereHas('bookings', fn ($query) => $query->whereIn('status', ['checked_in', 'checked_out']))->get();

        DB::transaction(function () use ($guestsWhoStayed): void {
            SupportRequest::factory(100)->recycle($guestsWhoStayed)->create();
        });
    }
}
