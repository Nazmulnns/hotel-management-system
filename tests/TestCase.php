<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
        protected function actingAsRole(UserRole $role, bool $isActive = true): User
    {
        $user = new User([
            'name' => 'Test Staff',
            'email' => 'staff@hms.test',
            'password' => 'secret123',
            'role' => $role,
            'is_active' => $isActive,
        ]);

        Sanctum::actingAs($user);

        return $user;
    }
}
