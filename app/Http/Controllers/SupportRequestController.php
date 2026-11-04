<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetSupportRequestsRequest;
use App\Http\Requests\StoreSupportRequestRequest;
use App\Http\Requests\UpdateSupportRequestRequest;
use App\Http\Resources\SupportRequestResource;
use App\Models\Guest;
use App\Services\SupportRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupportRequestController extends Controller
{
    public function __construct(private SupportRequestService $supportRequestService) 
    {
    }

    public function getSupportRequests(GetSupportRequestsRequest $request): AnonymousResourceCollection
    {
        return SupportRequestResource::collection($this->supportRequestService->getSupportRequests($request->validated()));
    }

    public function findSupportRequestById(int $id): SupportRequestResource|JsonResponse
    {
        $supportRequest = $this->supportRequestService->findSupportRequestById($id);

        if (empty($supportRequest)) {
            return response()->json(['message' => "No support request found with id {$id}"], 404);
        }

        return new SupportRequestResource($supportRequest);
    }

    public function createSupportRequest(StoreSupportRequestRequest $request, int $id): SupportRequestResource|JsonResponse
    {
        $guest = Guest::find($id);

        if (empty($guest)) {
            return response()->json(['message' => "No guest found with id {$id}"], 404);
        }

        return (new SupportRequestResource($this->supportRequestService->createSupportRequest($guest, $request->validated())))
            ->additional(['message' => 'Support request created successfully.']);
    }

    public function updateSupportRequest(UpdateSupportRequestRequest $request, int $id): SupportRequestResource|JsonResponse
    {
        $supportRequest = $this->supportRequestService->findSupportRequestById($id);

        if (empty($supportRequest)) {
            return response()->json(['message' => "No support request found with id {$id}"], 404);
        }

        return (new SupportRequestResource($this->supportRequestService->updateSupportRequest($supportRequest, $request->validated())))
            ->additional(['message' => 'Support request updated successfully.']);
    }
}
