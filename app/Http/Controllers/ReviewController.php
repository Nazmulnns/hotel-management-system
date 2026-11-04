<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetReviewsRequest;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) 
    {
    }

    public function getReviews(GetReviewsRequest $request): AnonymousResourceCollection
    {
        return ReviewResource::collection($this->reviewService->getReviews($request->validated()));
    }

    public function addReview(StoreReviewRequest $request, int $id): ReviewResource|JsonResponse
    {
        $booking = Booking::find($id);

        if (empty($booking)) {
            return response()->json(['message' => "No booking found with id {$id}"], 404);
        }

        return (new ReviewResource($this->reviewService->addReview($booking, $request->validated())))
            ->additional(['message' => 'Review added successfully.']);
    }
}
