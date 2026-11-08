<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CancelExpiredPendingBookings implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cancelledCount = Booking::query()
            ->where('status', BookingStatus::Pending)
            ->where('created_at', '<=', now()->subDay())
            ->limit(1)
            ->update(['status' => BookingStatus::Cancelled]);

        Log::info("Cancelled {$cancelledCount} pending booking(s) older than 24 hours.");
    }
}
