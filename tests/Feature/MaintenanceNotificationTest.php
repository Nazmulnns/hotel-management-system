<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use App\Notifications\MaintenanceReported;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MaintenanceNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $receptionist;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->receptionist = User::factory()->create();
        $this->room = Room::factory()->create();

        Sanctum::actingAs($this->receptionist);
    }

    private function reportIssue(): void
    {
        $this->postJson("/api/rooms/{$this->room->id}/maintenances", [
            'issue_description' => 'The AC is leaking water',
        ])->assertCreated();
    }

    public function test_every_active_manager_is_notified(): void
    {
        $managers = User::factory()->manager()->count(2)->create();

        $this->reportIssue();

        Notification::assertSentTo($managers, MaintenanceReported::class);
        Notification::assertCount(2);
    }

    public function test_a_deactivated_manager_is_not_notified(): void
    {
        User::factory()->manager()->create();
        $formerManager = User::factory()->manager()->inactive()->create();

        $this->reportIssue();

        Notification::assertNotSentTo($formerManager, MaintenanceReported::class);
    }

    public function test_other_staff_are_not_notified(): void
    {
        $housekeeper = User::factory()->housekeeping()->create();

        $this->reportIssue();

        Notification::assertNotSentTo([$this->receptionist, $housekeeper], MaintenanceReported::class);
    }

    public function test_the_notification_carries_the_reported_maintenance(): void
    {
        $manager = User::factory()->manager()->create();

        $this->reportIssue();

        Notification::assertSentTo(
            $manager,
            MaintenanceReported::class,
            function (MaintenanceReported $notification, array $channels) {
                return $notification->maintenance->room_id === $this->room->id
                    && $notification->maintenance->issue_description === 'The AC is leaking water'
                    && $channels === ['mail'];
            },
        );
    }

    public function test_nothing_is_sent_when_there_is_no_manager(): void
    {
        $this->reportIssue();

        Notification::assertNothingSent();
    }
}