<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetGuestRequest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use App\Services\GuestService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GuestController extends Controller
{
    private GuestService $guestService;

    public function __construct(GuestService $guestService)
    {
        $this->guestService = $guestService;
    }

    public function findGuestById(int $id): GuestResource
    {
        return new GuestResource($this->guestService->findGuestById($id));
    }

    public function getGuests(GetGuestRequest $request): AnonymousResourceCollection
    {
        $request = $request->validated();
        $guests = $this->guestService->getGuests($request);

        return GuestResource::collection($guests);
    }

    public function findGuestByIdentity(string $identity_number): ?int
    {
        return $this->guestService->findGuestByIdentityNumber($identity_number)?->id;
    }

    public function createGuest(StoreGuestRequest $request): GuestResource
    {
        $request = $request->validated();
        return (new GuestResource($this->guestService->createGuest($request))) ->additional(['message' => 'Guest added successfully.']);
    }
}
