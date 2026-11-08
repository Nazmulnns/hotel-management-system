<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    private const TAX_RATE = 0.15;

    private const DAYS_TO_PAY = 7;

    public function getInvoices(array $filters): LengthAwarePaginator
    {
        $query = Invoice::query()->with('booking.guest');

        if (isset($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query->whereLike('invoice_number', "%{$search}%")
                    ->orWhereRelation('booking', 'reference', 'like', "%{$search}%")
                    ->orWhereRelation('booking.guest', 'full_name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        if (isset($filters['booking_id'])) {
            $query->where('booking_id', $filters['booking_id']);
        }

        if (isset($filters['from'])) {
            $query->whereDate('issue_date', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->whereDate('issue_date', '<=', $filters['to']);
        }

        return $query
            ->orderBy($filters['sort_by'] ?? 'issue_date', $filters['sort_order'] ?? 'desc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function findInvoiceById(int $id): ?Invoice
    {
        return Invoice::with('booking.guest')->find($id);
    }

    public function generateInvoice(Booking $booking): Invoice
    {
        if (in_array($booking->status, [BookingStatus::Pending, BookingStatus::Cancelled], true)) {
            throw ValidationException::withMessages([
                'booking' => "An invoice cannot be made for a {$booking->status->label()} booking.",
            ]);
        }

        if ($booking->invoice()->exists()) {
            throw ValidationException::withMessages([
                'booking' => 'This booking already has an invoice.',
            ]);
        }

        return DB::transaction(function () use ($booking) {
            $invoice = $booking->invoice()->create([
                'invoice_number' => 'INV-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT),
                'issue_date' => today(),
                'due_date' => today()->addDays(self::DAYS_TO_PAY),
                'status' => InvoiceStatus::Issued,
                ...$this->calculateAmounts($booking),
            ]);

            $booking->update(['total_amount' => $invoice->grand_total]);

            return $invoice->load('booking.guest');
        });
    }

    public function updateInvoiceStatus(Invoice $invoice, InvoiceStatus $newStatus): Invoice
    {
        $invoice->update(['status' => $newStatus]);

        return $invoice->load('booking.guest');
    }

    private function calculateAmounts(Booking $booking): array
    {
        $nights = (int) $booking->check_in->diffInDays($booking->check_out);
        $lineItems = [];

        foreach ($booking->rooms as $room) {
            $lineItems[] = [
                'description' => "Room {$room->floor}{$room->room_number} × {$nights} nights",
                'amount' => round($room->pivot->price_per_night * $nights, 2),
            ];
        }

        $roomTotal = array_sum(array_column($lineItems, 'amount'));

        foreach ($booking->serviceOrders()->with('service')->get() as $serviceOrder) {
            $lineItems[] = [
                'description' => "{$serviceOrder->service->service_name} × {$serviceOrder->quantity}",
                'amount' => (float) $serviceOrder->total_price,
            ];
        }

        $subtotal = round(array_sum(array_column($lineItems, 'amount')), 2);
        $discountTotal = round((float) $booking->discounts->sum('pivot.amount_saved'), 2);
        $taxTotal = round(($subtotal - $discountTotal) * self::TAX_RATE, 2);

        return [
            'room_total' => round($roomTotal, 2),
            'services_total' => round($subtotal - $roomTotal, 2),
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'grand_total' => round($subtotal - $discountTotal + $taxTotal, 2),
            'line_items' => $lineItems,
        ];
    }
}