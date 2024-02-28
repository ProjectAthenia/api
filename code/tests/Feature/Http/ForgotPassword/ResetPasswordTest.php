<?php
declare(strict_types=1);

namespace Tests\Feature\Http\ForgotPassword;

use App\Models\User\PasswordToken;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ResetPasswordTest
 * @package Tests\Feature\Http\ForgotPassword
 */
class ResetPasswordTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    private $route = '/v1/reset-password';

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
        $this->setupDatabase();
    }

    public function testMissingRequiredFields()
    {
        $response = $this->json('POST', $this->route);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email field is required.',
                ],
                'token' => [
                    'The token field is required.',
                ],
                'password' => [
                    'The password field is required.',
                ],
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testStringFieldsTooLong()
    {
        $response = $this->json('POST', $this->route, [
            'email' => str_repeat('a', 121),
            'token' => str_repeat('a', 41),
            'password' => str_repeat('a', 121),
        ]);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email may not be greater than 120 characters.',
                ],
                'token' => [
                    'The token may not be greater than 40 characters.',
                ],
                'password' => [
                    'The password may not be greater than 120 characters.',
                ],
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testEmailFormatIncorrect()
    {
        $response = $this->json('POST', $this->route, [
            'email' => 'bryce',
        ]);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email must be a valid email address.',
                ],
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testModelsDoNotExist()
    {
        $response = $this->json('POST', $this->route, [
            'email' => 'guy@smiley.com',
            'token' => 'hello'
        ]);

        $response->assertJson([
            'errors' => [
                'email' => ['The selected email is invalid.'],
                'token' => ['The selected token is invalid.'],
            ],
        ]);
        $response->assertStatus(400);
    }

    public function testUserDoesNotOwnToken()
    {
        User::factory()->create([
            'email' => 'guy@smiley.com',
        ]);
        PasswordToken::factory()->create([
            'token' => 'hello',
        ]);

        $response = $this->json('POST', $this->route, [
            'email' => 'guy@smiley.com',
            'token' => 'hello'
        ]);

        $response->assertJson([
            'errors' => [
                'token' => ['The reset password token does not seem to be for the entered email address.'],
            ],
        ]);
        $response->assertStatus(400);
    }

    public function testTokenExpired()
    {
        PasswordToken::factory()->create([
            'token' => 'hello',
            'created_at' => Carbon::now()->subMinutes(21),
            'user_id' => User::factory()->create([
                'email' => 'guy@smiley.com',
            ])->id,
        ]);

        $response = $this->json('POST', $this->route, [
            'email' => 'guy@smiley.com',
            'token' => 'hello'
        ]);

        $response->assertJson([
            'errors' => [
                'token' => ['The reset password token has expired. You are going to have to request a new one.'],
            ],
        ]);
        $response->assertStatus(400);
    }

    public function testSuccess()
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        PasswordToken::factory()->create([
            'token' => 'hello',
            'user_id' => $user->id,
        ]);

        $response = $this->json('POST', $this->route, [
            'email' => 'test@test.com',
            'token' => 'hello',
            'password' => '12345678',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK'
        ]);

        /** @var User $updated */
        $updated = User::find($user->id);

        $this->assertTrue(Hash::check('12345678', $updated->password));
    }
}
