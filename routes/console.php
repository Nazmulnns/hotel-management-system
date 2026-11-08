<?php

use App\Enums\BookingStatus;
use App\Jobs\CancelExpiredPendingBookings;
use App\Models\Booking;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('inspire')->everyMinute()->appendOutputTo(storage_path('logs/schedule.log'));
Schedule::command('maintenances:send-weekly-report')->weeklyOn(0, '09:00');

Schedule::job(new CancelExpiredPendingBookings())->hourly();

Schedule::call(function () {
    $checkIns = Booking::whereDate('check_in', today())
        ->where('status', BookingStatus::Confirmed)
        ->count();

    $checkOuts = Booking::whereDate('check_out', today())
        ->where('status', BookingStatus::CheckedIn)
        ->count();

    Log::info("Front desk today: {$checkIns} check-in(s), {$checkOuts} check-out(s).");
})->name('front-desk-summary')->dailyAt('07:00');

Schedule::exec('node ' . base_path('scripts/clean-old-logs.js'))
    ->weekly()
    ->appendOutputTo(storage_path('logs/schedule.log'));