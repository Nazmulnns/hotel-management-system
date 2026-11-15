<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\RoomMaintenance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomMaintenancePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isManager()) {
            return true;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, RoomMaintenance $maintenance, array $data): Response
    {
        if (! $user->hasRole(UserRole::Housekeeping)) {
            return Response::deny('You cannot update maintenance records.');
        }

        if (array_key_exists('issue_description', $data)) {
            return Response::deny('Only a manager can change the issue description.');
        }

        return Response::allow();
    }
}