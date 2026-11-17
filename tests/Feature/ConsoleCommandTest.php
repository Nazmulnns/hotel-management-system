<?php

namespace Tests\Feature;

use App\Jobs\CancelExpiredPendingBookings;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConsoleCommandTest extends TestCase
{
    public function test_the_report_fails_when_the_manager_email_is_missing(): void
    {
        Mail::fake();
        config(['mail.manager_address' => null]);

        $this->artisan('maintenances:send-weekly-report')
            ->expectsOutput('Set MAIL_MANAGER_ADDRESS in .env first.')
            ->doesntExpectOutputToContain('Report sent')
            ->assertFailed();

        Mail::assertNothingSent();
    }

    public function test_the_weekly_report_runs_every_sunday_at_nine(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn (Event $event) => str_contains($event->command ?? '', 'maintenances:send-weekly-report'));

        $this->assertNotNull($event);
        $this->assertSame('0 9 * * 0', $event->expression);
    }

    public function test_expired_bookings_are_cancelled_every_hour(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn (Event $event) => $event->description === CancelExpiredPendingBookings::class);

        $this->assertInstanceOf(CallbackEvent::class, $event);
        $this->assertSame('0 * * * *', $event->expression);
    }

    public function test_the_inspire_command_prints_a_quote(): void
    {
        $this->artisan('inspire')->assertSuccessful();
    }
}