<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getUsers(array $filters): LengthAwarePaginator
    {
        $query = User::query();

        if (isset($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query->whereLike('name', "%{$search}%")
                    ->orWhereLike('email', "%{$search}%")
                    ->orWhereLike('phone_number', "%{$search}%");
            });
        }

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query
            ->orderBy($filters['sort_by'] ?? 'name', $filters['sort_order'] ?? 'asc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function findUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        return User::create([
            ...$data,
            'is_active' => true,
        ]);
    }

    public function updateUser(User $user, array $data, User $actingUser): User
    {
        if (isset($data['role'])) {
            $newRole = UserRole::from($data['role']);

            if ($user->is($actingUser) && $newRole !== UserRole::Manager) {
                throw ValidationException::withMessages([
                    'role' => 'You cannot remove your own manager role.',
                ]);
            }

            if ($user->isManager() && $newRole !== UserRole::Manager) {
                $this->ensureAnotherActiveManagerExists($user);
            }
        }

        $user->update($data);

        return $user;
    }

    public function deactivateUser(User $user, User $actingUser): User
    {
        if ($user->is($actingUser)) {
            throw ValidationException::withMessages([
                'user' => 'You cannot deactivate your own account.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'user' => 'This account is already deactivated.',
            ]);
        }

        if ($user->isManager()) {
            $this->ensureAnotherActiveManagerExists($user);
        }

        $user->update(['is_active' => false]);
        $user->tokens()->delete();

        return $user;
    }

    public function activateUser(User $user): User
    {
        if ($user->is_active) {
            throw ValidationException::withMessages([
                'user' => 'This account is already active.',
            ]);
        }

        $user->update(['is_active' => true]);

        return $user;
    }

    private function ensureAnotherActiveManagerExists(User $user): void
    {
        $otherActiveManagers = User::active()
            ->withRole(UserRole::Manager)
            ->whereKeyNot($user->id)
            ->exists();

        if (! $otherActiveManagers) {
            throw ValidationException::withMessages([
                'user' => 'At least one active manager must remain.',
            ]);
        }
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
