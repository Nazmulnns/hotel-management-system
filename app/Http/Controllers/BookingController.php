<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\GetBookingsRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) 
    {
    }

    public function getBookings(GetBookingsRequest $request): AnonymousResourceCollection
    {
        return BookingResource::collection($this->bookingService->getBookings($request->validated()));
    }

    public function findBookingById(int $id): BookingResource|JsonResponse
    {
        $booking = $this->bookingService->findBookingById($id);

        if (empty($booking)) {
            return response()->json(['message' => "No booking found with id {$id}"], 404);
        }

        return new BookingResource($booking);
    }

    public function createBooking(StoreBookingRequest $request): BookingResource
    {
        return (new BookingResource($this->bookingService->createBooking($request->validated())))
            ->additional(['message' => 'Booking created successfully.']);
    }

    public function updateBookingStatus(UpdateBookingStatusRequest $request, int $id): BookingResource|JsonResponse
    {
        $booking = $this->bookingService->findBookingById($id);

        if (empty($booking)) {
            return response()->json(['message' => "No booking found with id {$id}"], 404);
        }

        $newStatus = BookingStatus::from($request->validated('status'));

        return (new BookingResource($this->bookingService->updateStatus($booking, $newStatus)))
            ->additional(['message' => "Booking is now {$newStatus->label()}."]);
    }
}
