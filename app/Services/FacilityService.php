<?php

namespace App\Services;

use App\Models\Facility;
use Exception;

class FacilityService
{
    public function getAll()
    {
        return Facility::all();
    }

    public function isFacilityNameExists(string $facilityName): bool
    {
        return Facility::where('facility_name', $facilityName)->exists();
    }

    public function findFacilityById(int $facilityId): ?Facility
    {
        return Facility::find($facilityId);
    }

    public function updateFacilityNameById(Facility $facility, string $newFacilityName): ?Facility
    {
        $facility->facility_name = strtoupper(str_replace('"', '', $newFacilityName));
        $facility->save();
        return $facility;
    }

    public function addNewFacility(string $facilityName)
    {
        $newFacility = new Facility();
        $newFacility->facility_name = strtoupper(str_replace('"', '', $facilityName));
        $newFacility->save();
        return $newFacility;
    }

    public function deleteFacility(Facility $facility): bool
    {
        $facility->rooms()->detach();
        return $facility->delete();
    }
}