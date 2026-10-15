<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function getUsers(): JsonResponse
    {
        $user = $this->userService->getall();
        return response()->json(['data' => $user]);
    }

    public function getUserById(Request $request): JsonResponse
    {
        $user = $this->userService->getUserById($request->id);
        if (empty($user)) {
            return response()->json("No user found with id = {$request->id}");
        }
        return response()->json(['data' => $user]);
    }

    public function getUserByUsername(Request $request): JsonResponse
    {
        $user = $this->userService->getUserByUsername($request->username);
        if (empty($user)) {
            return response()->json("No user found with id = {$request->username}");
        }
        return response()->json(['data' => $user]);
    }

    public function updateUser(Request $request): JsonResponse
    {
        $user = $this->userService->getUserById($request->id);
        if (empty($user)) {
            return response()->json("No user found with id = {$request->id}");
        }
    }
}
