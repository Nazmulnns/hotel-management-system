<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::whereHas('booking', fn ($query) => $query->whereIn('status', ['checked_in', 'checked_out']))
                ->doesntHave('payments')
                ->get()
                ->each(function (Invoice $invoice): void {
                    $paymentFactory = Payment::factory()->for($invoice);

                    if (fake()->boolean(10)) {
                        $paymentFactory = $paymentFactory->unchecked();
                    }

                    $paymentFactory->create();

                    $invoice->update(['status' => 'paid']);
                });
    }
}
