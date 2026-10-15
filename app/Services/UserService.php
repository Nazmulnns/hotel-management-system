<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function getAll()
    {
        return User::all();
    }

    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function getUserByUsername(string $username): ?User
    {
        return User::where('username', $username);
    }
}