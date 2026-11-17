<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Revenue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * @return Collection<int, Revenue>
     */
    public function getMonthlyRevenue(int $year): Collection
    {
        return Revenue::query()
            ->selectRaw("DATE_FORMAT(revenue_date, '%Y-%m') as month")
            ->selectRaw('COUNT(*) as payment_count')
            ->selectRaw('SUM(amount) as total_amount')
            ->whereYear('revenue_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * @return array<int, object>
     */
    public function getRoomOccupancy(string $from, string $to): array
    {
        return DB::select('CALL room_occupancy(?, ?)', [$from, $to]);
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookingNights(int $year): Collection
    {
        return Booking::query()
            ->selectRaw("DATE_FORMAT(check_in, '%Y-%m') as month")
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw('SUM(booking_nights(check_in, check_out)) as total_nights')
            ->selectRaw('ROUND(AVG(booking_nights(check_in, check_out)), 1) as average_nights')
            ->whereYear('check_in', $year)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::CheckedIn, BookingStatus::CheckedOut])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
