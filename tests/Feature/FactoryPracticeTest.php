<?php

namespace Tests\Feature;

use App\Enums\MaintenanceStatus;
use App\Enums\UserRole;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FactoryPracticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_an_user_without_saving_it(): void
    {
        $user = User::factory()->make();

        $this->assertFalse($user->exists());
        $this->assertNull($user->id);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_create_saves_users(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->exists());
        $this->assertNotNull($user->id);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_count_creates_many_users(): void
    {
        $users = User::factory()->count(5)->create();

        $this->assertDatabaseCount('users', 5);
        $this->assertCount(5, $users);
    }

    public function test_values_given_to_create_replace_the_fake_ones(): void
    {
        User::factory()->create(['name' => 'Hotel Manager', 'email' => 'manager@hms.test']);

        $this->assertDatabaseHas('users', ['name' => 'Hotel Manager', 'email' => 'manager@hms.test']);
    }

    public function test_states_can_be_joined_together(): void
    {
        $formerManager = User::factory()->manager()->inactive()->create();

        $this->assertSame(UserRole::Manager, $formerManager->role);
        $this->assertFalse($formerManager->is_active);
    }

    public function test_for_puts_every_maintenance_in_the_same_room(): void
    {
        $room = Room::factory()->create();

        RoomMaintenance::factory()->count(2)->for($room)->create();

        $this->assertDatabaseCount('rooms', 1);
        $this->assertSame(2, $room->maintenances()->count());
    }

    public function test_without_for_each_maintenance_gets_its_own_room(): void
    {
        RoomMaintenance::factory()->count(2)->create();

        $this->assertDatabaseCount('rooms', 2);
    }

    public function test_has_creates_a_room_with_its_maintenances(): void
    {
        $room = Room::factory()
            ->has(RoomMaintenance::factory()->count(3), 'maintenances')
            ->create();

        $this->assertCount(3, $room->maintenances);
    }

    public function test_sequence_gives_each_row_the_next_value(): void
    {
        $maintenances = RoomMaintenance::factory()
            ->count(3)
            ->state(new Sequence(
                ['maintenance_status' => MaintenanceStatus::Pending],
                ['maintenance_status' => MaintenanceStatus::Resolved],
            ))
            ->create();

        $this->assertSame(MaintenanceStatus::Pending, $maintenances[0]->maintenance_status);
        $this->assertSame(MaintenanceStatus::Resolved, $maintenances[1]->maintenance_status);
        $this->assertSame(MaintenanceStatus::Pending, $maintenances[2]->maintenance_status);
    }
}
