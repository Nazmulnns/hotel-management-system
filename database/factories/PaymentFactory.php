<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::inRandomOrder()->value('id') ?? Invoice::factory(),
            'booking_id' => fn (array $attributes) => Invoice::findOrFail($attributes['invoice_id'])->booking_id,
            'amount' => fn (array $attributes) => Invoice::findOrFail($attributes['invoice_id'])->grand_total,
            'payment_date' => function (array $attributes) {
                $invoice = Invoice::findOrFail($attributes['invoice_id']);

                $earliestDate = CarbonImmutable::parse($invoice->issue_date);
                $latestDate = CarbonImmutable::parse($invoice->due_date)->endOfDay()->min(CarbonImmutable::now());

                return fake()->dateTimeBetween($earliestDate, $latestDate);
            },
            'payment_method' => fake()->randomElement(['cash', 'card', 'bkash', 'nagad', 'bank_transfer']),
            'status' => 'checked',
            'transaction_id' => fn (array $attributes) => $attributes['payment_method'] === 'cash'
                ? 'CASH-' . fake()->unique()->numerify('########')
                : strtoupper(fake()->unique()->bothify('TXN##??##??##')),
        ];
    }

    public function unchecked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unchecked',
        ]);
    }
}
