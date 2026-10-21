<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Revenue;
use Illuminate\Database\Seeder;

class RevenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payment::where('status', 'checked')
            ->doesntHave('revenues')
            ->get()
            ->each(fn (Payment $payment) => Revenue::factory()->for($payment)->create());
    }
}
