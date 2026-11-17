<?php

namespace Tests\Feature;

use App\Enums\MaintenanceStatus;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoomMaintenanceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_reporting_an_issue_saves_a_pending_maintenance(): void
    {
        $room = Room::factory()->create();

        $this->postJson("/api/rooms/{$room->id}/maintenances", [
            'issue_description' => 'The AC is leaking water',
        ])->assertCreated();

        $this->assertDatabaseCount('room_maintenances', 1);
        $this->assertDatabaseHas('room_maintenances', [
            'room_id' => $room->id,
            'issue_description' => 'The AC is leaking water',
            'maintenance_status' => MaintenanceStatus::Pending,
        ]);
    }

    public function test_an_invalid_report_saves_nothing(): void
    {
        $room = Room::factory()->create();

        $this->postJson("/api/rooms/{$room->id}/maintenances", [
            'issue_description' => 'short',
        ])->assertUnprocessable();

        $this->assertDatabaseEmpty('room_maintenances');
    }
}