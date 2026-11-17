<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    private MockInterface $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = $this->mock(AuthService::class);
    }

    private function loginAlwaysFails(): void
    {
        $this->authService->allows('login')
            ->andThrow(ValidationException::withMessages(['email' => 'The provided credentials are incorrect.']));
    }

    private function tryWrongLogin(): TestResponse
    {
        return $this->postJson('/api/login', ['email' => 'reception@hms.test', 'password' => 'wrong']);
    }

    public function test_login_returns_a_token_and_hides_the_password(): void
    {
        $user = new User(['name' => 'Front Desk', 'email' => 'reception@hms.test', 'password' => 'secret123', 'role' => UserRole::Receptionist, 'is_active' => true]);

        $this->authService->expects('login')
            ->with('reception@hms.test', 'secret123', Mockery::type('string'))
            ->andReturn(['user' => $user, 'token' => '1|plain-text-token']);

        $this->postJson('/api/login', ['email' => 'reception@hms.test', 'password' => 'secret123'])
            ->assertOk()
            ->assertJsonPath('token', '1|plain-text-token')
            ->assertJsonPath('user.role', 'receptionist')
            ->assertJsonMissingPath('user.password');
    }

    public function test_login_needs_an_email_and_a_password(): void
    {
        $this->authService->shouldNotReceive('login');

        $this->postJson('/api/login', ['email' => 'not-an-email'])
            ->assertUnprocessable()
            ->assertInvalid(['email' => 'valid email', 'password' => 'required']);
    }

    public function test_wrong_credentials_return_422(): void
    {
        $this->loginAlwaysFails();

        $this->tryWrongLogin()
            ->assertUnprocessable()
            ->assertInvalid(['email' => 'The provided credentials are incorrect.']);
    }

    public function test_login_is_blocked_after_five_tries_in_a_minute(): void
    {
        $this->loginAlwaysFails();

        $this->tryWrongLogin()->assertHeader('X-RateLimit-Limit', 5);

        foreach (range(2, 5) as $try) {
            $this->tryWrongLogin();
        }

        $this->tryWrongLogin()->assertTooManyRequests();
    }

    public function test_the_limit_can_be_switched_off_inside_a_test(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        $this->loginAlwaysFails();

        foreach (range(1, 6) as $try) {
            $this->tryWrongLogin()->assertUnprocessable();
        }
    }

    public function test_me_returns_the_logged_in_user_without_secrets(): void
    {
        $this->actingAsRole(UserRole::Housekeeping);
        $this->authService->shouldNotReceive('login');

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'staff@hms.test')
            ->assertJsonPath('data.role', 'housekeeping')
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.remember_token');
    }
}