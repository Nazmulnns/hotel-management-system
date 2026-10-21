<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppliedDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->doesntHave('discounts')
                ->withSum('serviceOrders', 'total_price')
                ->get()
                ->each(function (Booking $booking): void {
                    if (! fake()->boolean(30)) {
                        return;
                    }

                    $discount = Discount::where('expiry_date', '>=', $booking->booking_date)->inRandomOrder()->first();

                    if ($discount === null) {
                        return;
                    }

                    $subtotal = $booking->total_amount + ($booking->service_orders_sum_total_price ?? 0);

                    $booking->discounts()->attach($discount->id, [
                        'amount_saved' => round($subtotal * $discount->percentage / 100, 2),
                    ]);
                });
    }
}
