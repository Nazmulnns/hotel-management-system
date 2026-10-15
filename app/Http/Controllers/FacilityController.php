<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Services\FacilityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    private FacilityService $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function getAllFacilities(): JsonResponse
    {
        $facilities = $this->facilityService->getAll();
        return response()->json(['data' => $facilities], 200);
    }

    public function updateFacility(StoreFacilityRequest $request): JsonResponse
    {
        try {
            $request->validated();
            $facilityId = $request->id;
            $facility = $this->facilityService->findFacilityById($facilityId);
            $preFacilityName = $facility->facility_name;

            if (empty($facility)) {
                return response()->json(['message' => "Facility does not exists with facility id {$facilityId}"], 404);
            }

            $this->facilityService->updateFacilityNameById($facility, $request->facility_name);
            return response()->json(['message' => "Facility {$preFacilityName} is changed to {$request->facility_name} successfully"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function addNewFacility(StoreFacilityRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $newFacilityName = $validated['facility_name'];
            if ($this->facilityService->isFacilityNameExists($newFacilityName)) {
                return response()->json(['message' => "Facility Name {$newFacilityName} is already exists"], 409);
            }

            $this->facilityService->addNewFacility($newFacilityName);
            return response()->json(['message' => "Facility Name {$newFacilityName} is successfully added"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
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
            return response()->json(['message' => "Something went wrong {$e}"], 500);
        }
    }
}
