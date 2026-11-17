<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    private MockInterface $userService;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = Mockery::mock(UserService::class);
        $this->authService = new AuthService($this->userService);
    }

    /**
     * A User double: reading $user->email, $user->is_active ... returns these values,
     * and no database is touched.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function userWith(array $attributes): MockInterface
    {
        $user = Mockery::mock(User::class);
        $user->allows('getAttribute')->andReturnUsing(fn (string $key) => $attributes[$key] ?? null);

        return $user;
    }

    public function test_login_returns_the_user_and_the_new_token(): void
    {
        $user = $this->userWith(['email' => 'reception@hms.test', 'password' => 'hashed-secret', 'is_active' => true]);
        $user->allows('update');
        $user->expects('createToken')
            ->with('Postman')
            ->andReturn(new NewAccessToken(new PersonalAccessToken, '1|plain-text-token'));

        $this->userService->expects('findUserByEmail')->with('reception@hms.test')->andReturn($user);
        Hash::expects('check')->with('secret123', 'hashed-secret')->andReturn(true);

        $result = $this->authService->login('reception@hms.test', 'secret123', 'Postman');

        $this->assertSame($user, $result['user']);
        $this->assertSame('1|plain-text-token', $result['token']);
    }

    public function test_login_saves_the_login_time(): void
    {
        $this->travelTo('2026-11-17 09:30:00');

        $user = $this->userWith(['password' => 'hashed-secret', 'is_active' => true]);
        $user->allows('createToken')->andReturn(new NewAccessToken(new PersonalAccessToken, 'token'));
        $user->expects('update')->with(Mockery::on(
            fn (array $data) => $data['last_login_at']->toDateTimeString() === '2026-11-17 09:30:00',
        ));

        $this->userService->allows('findUserByEmail')->andReturn($user);
        Hash::shouldReceive('check')->andReturn(true);

        $this->authService->login('reception@hms.test', 'secret123', 'Postman');
    }

    public function test_login_rejects_a_wrong_password(): void
    {
        $user = $this->userWith(['password' => 'hashed-secret', 'is_active' => true]);
        $user->shouldNotReceive('update');
        $user->shouldNotReceive('createToken');

        $this->userService->allows('findUserByEmail')->andReturn($user);
        Hash::expects('check')->with('wrong-password', 'hashed-secret')->andReturn(false);

        $this->assertLoginFailsWith('wrong-password', 'The provided credentials are incorrect.');
    }

    public function test_login_gives_an_unknown_email_the_same_message(): void
    {
        $this->userService->expects('findUserByEmail')->with('nobody@hms.test')->andReturn(null);
        Hash::shouldReceive('check')->never();

        try {
            $this->authService->login('nobody@hms.test', 'secret123', 'Postman');
            $this->fail('A ValidationException was expected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['The provided credentials are incorrect.'], $exception->errors()['email']);
        }
    }

    public function test_login_blocks_a_deactivated_user(): void
    {
        $user = $this->userWith(['password' => 'hashed-secret', 'is_active' => false]);
        $user->shouldNotReceive('update');
        $user->shouldNotReceive('createToken');

        $this->userService->allows('findUserByEmail')->andReturn($user);
        Hash::shouldReceive('check')->andReturn(true);

        $this->assertLoginFailsWith('secret123', 'This account has been deactivated.');
    }

    private function assertLoginFailsWith(string $password, string $message): void
    {
        try {
            $this->authService->login('reception@hms.test', $password, 'Postman');
            $this->fail('A ValidationException was expected.');
        } catch (ValidationException $exception) {
            $this->assertSame([$message], $exception->errors()['email']);
        }
    }

    public function test_logout_deletes_only_the_current_token(): void
    {
        $currentToken = Mockery::mock(PersonalAccessToken::class);
        $currentToken->expects('delete');

        $user = Mockery::mock(User::class);
        $user->allows('currentAccessToken')->andReturn($currentToken);
        $user->shouldNotReceive('tokens');

        $this->authService->logout($user);
    }

    public function test_change_password_saves_the_new_password(): void
    {
        $tokens = Mockery::mock(MorphMany::class);
        $tokens->allows('whereKeyNot')->andReturnSelf();
        $tokens->allows('delete');

        $user = Mockery::mock(User::class);
        $user->allows('tokens')->andReturn($tokens);
        $user->allows('currentAccessToken')->andReturn((new PersonalAccessToken)->forceFill(['id' => 5]));
        $user->expects('update')->with(['password' => 'brandnew99']);

        $this->authService->changePassword($user, 'brandnew99');
    }

    public function test_change_password_logs_out_every_other_device(): void
    {
        $tokens = Mockery::mock(MorphMany::class);
        $tokens->expects('whereKeyNot')->with(5)->andReturnSelf();
        $tokens->expects('delete');

        $user = Mockery::mock(User::class);
        $user->allows('update');
        $user->allows('tokens')->andReturn($tokens);
        $user->allows('currentAccessToken')->andReturn((new PersonalAccessToken)->forceFill(['id' => 5]));

        $this->authService->changePassword($user, 'brandnew99');
    }

    public function test_reset_link_is_asked_only_for_active_users(): void
    {
        Password::expects('sendResetLink')
            ->with(['email' => 'reception@hms.test', 'is_active' => true])
            ->andReturn(Password::ResetLinkSent);

        $this->authService->sendPasswordResetLink('reception@hms.test');
    }

    public function test_reset_link_for_an_unknown_email_raises_no_error(): void
    {
        Password::shouldReceive('sendResetLink')->andReturn(Password::InvalidUser);

        $this->authService->sendPasswordResetLink('nobody@hms.test');

        $this->assertTrue(true);
    }

    public function test_reset_link_cannot_be_asked_again_too_soon(): void
    {
        Password::shouldReceive('sendResetLink')->andReturn(Password::ResetThrottled);

        $this->expectException(ValidationException::class);

        $this->authService->sendPasswordResetLink('reception@hms.test');
    }

    public function test_reset_saves_the_new_password_and_logs_out_every_device(): void
    {
        $tokens = Mockery::mock(MorphMany::class);
        $tokens->expects('delete');

        $user = Mockery::mock(User::class);
        $user->expects('update')->with(['password' => 'new-password']);
        $user->allows('tokens')->andReturn($tokens);

        $data = ['email' => 'reception@hms.test', 'token' => 'reset-token', 'password' => 'new-password', 'password_confirmation' => 'new-password'];

        Password::expects('reset')
            ->with($data, Mockery::type('callable'))
            ->andReturnUsing(function (array $credentials, callable $callback) use ($user) {
                $callback($user, $credentials['password']);

                return Password::PasswordReset;
            });

        $this->authService->resetPassword($data);
    }

    public function test_reset_with_a_bad_or_expired_token_is_rejected(): void
    {
        Password::shouldReceive('reset')->andReturn(Password::InvalidToken);

        try {
            $this->authService->resetPassword(['email' => 'reception@hms.test', 'token' => 'wrong', 'password' => 'x', 'password_confirmation' => 'x']);
            $this->fail('A ValidationException was expected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['This password reset link is invalid or has expired.'], $exception->errors()['email']);
        }
    }
}
