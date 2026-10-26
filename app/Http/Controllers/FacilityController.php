<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Services\FacilityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacilityController extends Controller
{
    private FacilityService $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function getAllFacilities(): AnonymousResourceCollection
    {
        $facilities = $this->facilityService->getAll();
        return FacilityResource::collection($facilities);
    }

    public function updateFacility(StoreFacilityRequest $request, int $facilityId): FacilityResource|JsonResponse
    {
        try {
            $request->validated();
            $facility = $this->facilityService->findFacilityById($facilityId);

            if (empty($facility)) {
                return response()->json(['message' => "Facility does not exists with facility id {$facilityId}"], 404);
            }

            $preFacilityName = $facility->facility_name;

            $updatedFacility = $this->facilityService->updateFacilityNameById($facility, $request->facility_name);
            return (new FacilityResource($updatedFacility))
                ->additional(['message' => "Facility {$preFacilityName} is changed to {$request->facility_name} successfully"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"]);
        }
    }

    public function addNewFacility(StoreFacilityRequest $request): FacilityResource|JsonResponse
    {
        try {
            $validated = $request->validated();
            $newFacilityName = $validated['facility_name'];

            return (new FacilityResource($this->facilityService->addNewFacility($newFacilityName)))
                ->additional(['message' => "Facility Name {$newFacilityName} is successfully added"]);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"]);
        }
    }

    public function deleteFacilityById(int $facilityId): JsonResponse
    {
        try {
            $facility = $this->facilityService->findFacilityById($facilityId);
            if (empty($facility)) {
                return response()->json(['message' => "Facility does not exists with facility id {$facilityId}"], 404);
            }

            $this->facilityService->deleteFacility($facility);
            return response()->json(['message' => "Facility {$facility->facility_name} is deleted successfully"], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"]);
        }
    }
}
