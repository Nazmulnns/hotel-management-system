<?php

namespace App\Services;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class FacilityService
{
    private const CACHE_KEY = 'facilities';

    /**
     * @return Collection<int, Facility>
     */
    public function getAll(): Collection
    {
        $facilities = Cache::rememberForever(self::CACHE_KEY, fn () => Facility::all()->toArray());

        return Facility::hydrate($facilities);
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
        $facility->facility_name = $newFacilityName;
        $facility->save();

        Cache::forget(self::CACHE_KEY);

        return $facility;
    }

    public function addNewFacility(string $facilityName)
    {
        $newFacility = new Facility;
        $newFacility->facility_name = $facilityName;
        $newFacility->save();

        Cache::forget(self::CACHE_KEY);

        return $newFacility;
    }

    public function deleteFacility(Facility $facility): void
    {
        $facility->rooms()->detach();
        $facility->delete();

        Cache::forget(self::CACHE_KEY);
    }
}
