<?php

namespace Tests\Unit;

use App\Enums\MaintenanceStatus;
use App\Models\Room;
use App\Services\RoomMaintenanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use PHPUnit\Framework\TestCase;

class RoomMaintenanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoomMaintenanceService $service;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RoomMaintenanceService;
        $this->room = Room::factory()->create(['floor' => 2, 'room_number' => 'A']);
    }

    public function test_it_saves_new_issue_as_pending(): void
    {
        Notification::fake();

        $maintenance = $this->service->reportMaintenance($this->room, [
            'issue_description' => 'AC is not cooling at all',
        ]);

        $this->assertSame(MaintenanceStatus::Pending, $maintenance->maintenance_status);
        $this->assertDatabaseHas('room_maintenances', [
            'id' => $maintenance->id,
            'room_id' => $this->room->id,
            'maintenance_status' => 'pending',
        ]);
    }
}
