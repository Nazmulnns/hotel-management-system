<?php

namespace Database\Seeders;

use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $discounts = [
            // Active
            ['discount_code' => 'WELCOME10',   'percentage' => 10, 'expiry_date' => $now->copy()->addMonths(6)],
            ['discount_code' => 'FIRSTSTAY15', 'percentage' => 15, 'expiry_date' => $now->copy()->addMonths(3)],
            ['discount_code' => 'WEEKEND20',   'percentage' => 20, 'expiry_date' => $now->copy()->addMonth()],
            ['discount_code' => 'LONGSTAY25',  'percentage' => 25, 'expiry_date' => $now->copy()->addMonths(4)],
            ['discount_code' => 'WINTER2026',  'percentage' => 18, 'expiry_date' => Carbon::create(2027, 2, 28)],
            ['discount_code' => 'EID30',       'percentage' => 30, 'expiry_date' => $now->copy()->addWeeks(2)],
            ['discount_code' => 'FAMILY12',    'percentage' => 12, 'expiry_date' => $now->copy()->addMonths(2)],
            ['discount_code' => 'CORPORATE15', 'percentage' => 15, 'expiry_date' => $now->copy()->addYear()],
            ['discount_code' => 'HONEYMOON22', 'percentage' => 22, 'expiry_date' => $now->copy()->addMonths(5)],
            ['discount_code' => 'STUDENT8',    'percentage' => 8,  'expiry_date' => $now->copy()->addMonths(6)],

            // Ekhuni expire hobe (test er jonno)
            ['discount_code' => 'FLASH25',     'percentage' => 25, 'expiry_date' => $now->copy()->addDays(2)],

            // Expired (test er jonno)
            ['discount_code' => 'SUMMER2026',  'percentage' => 20, 'expiry_date' => Carbon::create(2026, 8, 31)],
            ['discount_code' => 'MONSOON10',   'percentage' => 10, 'expiry_date' => $now->copy()->subDays(10)],
        ];

        foreach ($discounts as $discount) {
            Discount::updateOrCreate(['discount_code' => $discount['discount_code']], $discount);
        }
    }
}
