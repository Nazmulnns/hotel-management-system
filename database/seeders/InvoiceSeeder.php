<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->doesntHave('invoice')
            ->with('rooms')
            ->get()
            ->each(function (Booking $booking): void {
                $invoice = Invoice::factory()->for($booking)->create();

                $booking->update(['total_amount' => $invoice->grand_total]);
            });
    }
}
