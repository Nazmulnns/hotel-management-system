<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $serviceService)
    {
    }

    public function getServices(): AnonymousResourceCollection
    {
        return ServiceResource::collection($this->serviceService->getAllServices());
    }

    public function findServiceById(int $id): ServiceResource|JsonResponse
    {
        $service = $this->serviceService->findServiceById($id);

        if (empty($service)) {
            return response()->json(['message' => "No service found with id {$id}"], 404);
        }

        return new ServiceResource($service);
    }

    public function createNewService(StoreServiceRequest $request): ServiceResource
    {
        $request = $request->validated();
        return (new ServiceResource($this->serviceService->createNewService($request)))
                    ->additional(['message' => "New Service added successfully"]);
    }

    public function updateService(int $id, UpdateServiceRequest $request): ServiceResource|JsonResponse
    {
        $request = $request->validated();
        $service = $this->serviceService->findServiceById($id);

        if (empty($service)) {
            return response()->json(['message' => "No service found with id {$id}"], 404);
        }

        return (new ServiceResource($this->serviceService->updateService($service, $request)))
                    ->additional(['message' => "Service Updated Successfully"]);
    }

    public function deleteService(int $id): JsonResponse
    {
        $service = $this->serviceService->findServiceById($id);

        if (empty($service)) {
            return response()->json(['message' => "No service found with id {$id}"], 404);
        }

        $this->serviceService->delete($service);
        return response()->json(['message' => "Service deleted successfully with id {$id}"]);
    }

}
