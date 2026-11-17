<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetMonthlyRevenueRequest;
use App\Http\Requests\GetRoomOccupancyRequest;
use App\Http\Resources\BookingNightsResource;
use App\Http\Resources\MonthlyRevenueResource;
use App\Http\Resources\RoomOccupancyResource;
use App\Services\ReportService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function getMonthlyRevenue(GetMonthlyRevenueRequest $request): AnonymousResourceCollection
    {

        $year = (int) $request->validated('year', now()->year);

        return MonthlyRevenueResource::collection($this->reportService->getMonthlyRevenue($year))
            ->additional(['year' => $year]);
    }

    public function getRoomOccupancy(GetRoomOccupancyRequest $request): AnonymousResourceCollection
    {
        $from = $request->validated('from');
        $to = $request->validated('to');

        return RoomOccupancyResource::collection($this->reportService->getRoomOccupancy($from, $to))
            ->additional(['from' => $from, 'to' => $to]);
    }

    public function getBookingNights(GetMonthlyRevenueRequest $request): AnonymousResourceCollection
    {
        $year = (int) $request->validated('year', now()->year);

        return BookingNightsResource::collection($this->reportService->getBookingNights($year))
            ->additional(['year' => $year]);
    }
}
