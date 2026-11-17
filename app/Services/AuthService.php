<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private UserService $userService) {}
    /**
     * @return array{user: User, token: string}
     */
    public function login(string $email, string $password, string $deviceName): array
    {
        $user = $this->userService->findUserByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated.',
            ]);
        }

        $user->update(['last_login_at' => now()]);

        return [
            'user' => $user,
            'token' => $user->createToken($deviceName)->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function sendPasswordResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email, 'is_active' => true]);

        if ($status === Password::ResetThrottled) {
            throw ValidationException::withMessages([
                'email' => 'Please wait before asking for another reset link.',
            ]);
        }
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset($data, function (User $user, string $password) {
            $user->update(['password' => $password]);
            $user->tokens()->delete();
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => 'This password reset link is invalid or has expired.',
            ]);
        }
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->update(['password' => $newPassword]);

        $user->tokens()->whereKeyNot($user->currentAccessToken()->id)->delete();
    }
}