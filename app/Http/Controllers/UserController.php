<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetUsersRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function getUsers(GetUsersRequest $request): AnonymousResourceCollection
    {
        return UserResource::collection($this->userService->getUsers($request->validated()));
    }

    public function findUserById(int $id): UserResource|JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (empty($user)) {
            return response()->json(['message' => "No user found with id {$id}"], 404);
        }

        return new UserResource($user);
    }

    public function createUser(StoreUserRequest $request): UserResource
    {
        return (new UserResource($this->userService->createUser($request->validated())))
            ->additional(['message' => 'User created successfully.']);
    }

    public function updateUser(UpdateUserRequest $request, int $id): UserResource|JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (empty($user)) {
            return response()->json(['message' => "No user found with id {$id}"], 404);
        }

        return (new UserResource($this->userService->updateUser($user, $request->validated(), $request->user())))
            ->additional(['message' => 'User updated successfully.']);
    }

    public function deactivateUser(Request $request, int $id): UserResource|JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (empty($user)) {
            return response()->json(['message' => "No user found with id {$id}"], 404);
        }

        return (new UserResource($this->userService->deactivateUser($user, $request->user())))
            ->additional(['message' => 'User deactivated successfully.']);
    }

    public function activateUser(int $id): UserResource|JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (empty($user)) {
            return response()->json(['message' => "No user found with id {$id}"], 404);
        }

        return (new UserResource($this->userService->activateUser($user)))
            ->additional(['message' => 'User activated successfully.']);
    }
}
