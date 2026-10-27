<?php

namespace App\Services;

use App\Models\Service;

class ServiceService
{
    public function getAllServices()
    {
        return Service::all();
    }

    public function findServiceByName(string $serviceName): ?Service
    {
        return Service::where('service_name', $serviceName);
    }

    public function findServiceById(int $id): ?Service
    {
        return Service::find($id);
    }

    public function createNewService(array $data): ?Service
    {
        return Service::create($data);
    }

    public function updateService(Service $service, ?array $data): ?Service
    {
        $service->update($data);
        return $service;
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }
}
