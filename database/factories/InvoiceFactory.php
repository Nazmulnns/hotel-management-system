<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Invoice;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    private const TAX_RATE = 0.15;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::isRandomOrder()->value('id') ?? Booking::factory()->withRoom(),
            'invoice_number' => fn (array $attributes) => 'INV-' . str_pad((string) $attributes['booking_id'], 6, '0', STR_PAD_LEFT),
            'status' => 'issued',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Invoice $invoice): void {
            $invoice->fill($this->amountsForBooking(Booking::findOrFail($invoice->booking_id)));
        });
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    private function amountsForBooking(Booking $booking): array
    {
        $nights = (int) CarbonImmutable::parse($booking->check_in)->diffInDays($booking->check_out);

        $lineItems = [];
        $roomTotal = 0;

        foreach ($booking->rooms as $room) {
            $amount = round($room->pivot->price_per_night * $nights, 2);
            $roomTotal += $amount;
            $lineItems[] = [
                'description' => "Room {$room->floor}{$room->room_number} × {$nights} nights",
                'amount' => $amount,
            ];
        }

        $servicesTotal = 0;

        foreach ($booking->serviceOrders()->with('service')->get() as $serviceOrder) {
            $servicesTotal += $serviceOrder->total_price;
            $lineItems[] = [
                'description' => "{$serviceOrder->service->service_name} × {$serviceOrder->quantity}",
                'amount' => (float) $serviceOrder->total_price,
            ];
        }

        $subtotal = round($roomTotal + $servicesTotal, 2);
        $discountTotal = 0;
        $taxTotal = round(($subtotal - $discountTotal) * self::TAX_RATE, 2);

        return [
            'issue_date' => $booking->booking_date,
            'due_date' => $booking->check_in,
            'room_total' => $roomTotal,
            'services_total' => round($servicesTotal, 2),
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'grand_total' => round($subtotal - $discountTotal + $taxTotal, 2),
            'line_items' => $lineItems,
        ];
    }
}
