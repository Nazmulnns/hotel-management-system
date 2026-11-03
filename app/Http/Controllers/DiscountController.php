<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetDiscountsRequest;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DiscountController extends Controller
{
    public function __construct(private DiscountService $discountService)
    {
    }

    public function getDiscounts(GetDiscountsRequest $request): AnonymousResourceCollection
    {
        $request = $request->validated();
        return DiscountResource::collection($this->discountService->getDiscounts($request));
    }

    public function findDiscountById(int $id): DiscountResource|JsonResponse
    {
        $discount = $this->discountService->findDiscountById($id);
        if (empty($discount)) {
            return response()->json(['message' => "No discount found with id {$id}"], 404);
        }

        return new DiscountResource($discount);
    }

    public function addNewDiscount(StoreDiscountRequest $request): DiscountResource|JsonResponse
    {
        $request = $request->validated();
        
        return (new DiscountResource($this->discountService->addNewDiscount($request)))
                ->additional(['message' => "Discount added successfully."]);
    }

    public function updateDiscount(int $id, UpdateDiscountRequest $request): DiscountResource|JsonResponse
    {
        $request = $request->validated();
        $discount = $this->discountService->findDiscountById($id);
        if (empty($discount)) {
            return response()->json(['message' => "No discount found with id {$id}"], 404);
        }

        return (new DiscountResource($this->discountService->updateDiscount($discount, $request)))
            ->additional(['message' => "Discount updated successfully."]);
    }

    public function deleteDiscountById(int $id): JsonResponse
    {
        $discount = $this->discountService->findDiscountById($id);
        if (empty($discount)) {
            return response()->json(['message' => "No discount found with id {$id}"], 404);
        }

        $this->discountService->deleteDiscount($discount);
        
        return response()->json(['message' => "Discount with id {$id} deleted Successfully"], 404);
    }
}
