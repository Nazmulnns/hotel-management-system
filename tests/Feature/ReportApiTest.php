<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Revenue;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    private const OCCUPANCY_URL = '/api/reports/room-occupancy?from=2026-11-01&to=2026-12-01';

    private MockInterface $reportService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reportService = $this->mock(ReportService::class);
    }

    public function test_a_guest_gets_401(): void
    {
        $this->getJson(self::OCCUPANCY_URL)
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_a_receptionist_gets_403_with_the_right_reason(): void
    {
        $this->actingAsRole(UserRole::Receptionist);
        $this->reportService->shouldNotReceive('getRoomOccupancy');

        $this->getJson(self::OCCUPANCY_URL)
            ->assertForbidden()
            ->assertJsonPath('message', 'You do not have permission to do this.');
    }

    public function test_a_deactivated_manager_gets_403_with_a_different_reason(): void
    {
        $this->actingAsRole(UserRole::Manager, isActive: false);

        $this->getJson(self::OCCUPANCY_URL)
            ->assertForbidden()
            ->assertJsonPath('message', 'Your account has been deactivated.');
    }

    public function test_a_missing_route_gets_404_and_a_wrong_method_gets_405(): void
    {
        $this->actingAsRole(UserRole::Manager);

        $this->getJson('/api/reports/does-not-exist')->assertNotFound();
        $this->deleteJson('/api/reports/room-occupancy')->assertMethodNotAllowed();
    }

    public function test_both_dates_are_required(): void
    {
        $this->actingAsRole(UserRole::Manager);
        $this->reportService->shouldNotReceive('getRoomOccupancy');

        $this->getJson('/api/reports/room-occupancy')
            ->assertUnprocessable()
            ->assertInvalid(['from' => 'required', 'to' => 'required']);
    }

    public function test_only_the_wrong_field_has_an_error(): void
    {
        $this->actingAsRole(UserRole::Manager);

        $this->getJson('/api/reports/room-occupancy?from=2026-12-01&to=2026-11-01')
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('to')
            ->assertJsonMissingValidationErrors(['from']);
    }

    public function test_validation_messages_follow_the_accept_language_header(): void
    {
        $this->actingAsRole(UserRole::Manager);

        $this->withHeaders(['Accept-Language' => 'bn'])
            ->getJson('/api/reports/room-occupancy')
            ->assertUnprocessable()
            ->assertJsonPath('errors.from.0', 'শুরুর তারিখ দিতে হবে।');
    }

    public function test_a_manager_gets_the_occupancy_report(): void
    {
        $this->actingAsRole(UserRole::Manager);

        $this->reportService->expects('getRoomOccupancy')
            ->with('2026-11-01', '2026-12-01')
            ->andReturn([
                (object) ['room_id' => 7, 'room_label' => '2A', 'booked_nights' => '9', 'total_nights' => 30, 'occupancy_percent' => '30.00'],
                (object) ['room_id' => 8, 'room_label' => '2B', 'booked_nights' => '0', 'total_nights' => 30, 'occupancy_percent' => '0.00'],
            ]);

        $this->getJson(self::OCCUPANCY_URL)
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => ['*' => ['room_id', 'room_label', 'booked_nights', 'total_nights', 'occupancy_percent']],
                'from',
                'to',
            ])
            ->assertJsonPaths([
                'data.0.room_label' => '2A',
                'data.0.booked_nights' => 9,
                'data.1.occupancy_percent' => 0,
                'from' => '2026-11-01',
                'to' => '2026-12-01',
            ])
            ->assertJsonFragment(['room_label' => '2B']);
    }

    public function test_the_monthly_revenue_response_is_exactly_this(): void
    {
        $this->actingAsRole(UserRole::Manager);

        $this->reportService->expects('getMonthlyRevenue')
            ->with(2026)
            ->andReturn(new Collection([
                (new Revenue)->forceFill(['month' => '2026-10', 'payment_count' => '23', 'total_amount' => '956956.73']),
            ]));

        $this->getJson('/api/reports/monthly-revenue?year=2026')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    ['month' => '2026-10', 'payment_count' => 23, 'total_amount' => 956956.73],
                ],
                'year' => 2026,
            ]);
    }

    public function test_booking_nights_uses_this_year_when_none_is_given(): void
    {
        $this->actingAsRole(UserRole::Manager);
        $this->travelTo('2027-03-15');

        $this->reportService->expects('getBookingNights')
            ->with(2027)
            ->andReturn(new Collection([
                (new Booking)->forceFill(['month' => '2027-01', 'booking_count' => '3', 'total_nights' => '9', 'average_nights' => '3.0']),
                (new Booking)->forceFill(['month' => '2027-02', 'booking_count' => '5', 'total_nights' => '20', 'average_nights' => '4.0']),
            ]));

        $this->getJson('/api/reports/booking-nights')
            ->assertOk()
            ->assertJsonPath('year', 2027)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.month', '2027-01')
            ->assertJsonPath('data.0.booking_count', 3)
            ->assertJsonMissingPath('from');
    }

    public function test_a_database_failure_returns_500(): void
    {
        $this->actingAsRole(UserRole::Manager);
        $this->reportService->allows('getRoomOccupancy')->andThrow(new RuntimeException('PROCEDURE room_occupancy does not exist'));

        $this->getJson(self::OCCUPANCY_URL)->assertServerError();
    }

    public function test_the_real_exception_can_be_seen_while_debugging(): void
    {
        $this->actingAsRole(UserRole::Manager);
        $this->reportService->allows('getRoomOccupancy')->andThrow(new RuntimeException('PROCEDURE room_occupancy does not exist'));

        $this->withoutExceptionHandling();
        $this->expectException(RuntimeException::class);

        $this->getJson(self::OCCUPANCY_URL);
    }
}