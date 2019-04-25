<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Authentication;

use Illuminate\Support\Facades\Hash;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class LoginTest
 * @package Tests\Feature\Http\Authentication
 */
class LoginTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
        $this->setupDatabase();
    }

    public function testMissingRequiredFields()
    {
        $response = $this->json('POST', '/v1/auth/login');

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email field is required.',
                ],
                'password' => [
                    'The password field is required.'
                ]
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testStringFieldsTooLong()
    {
        $response = $this->json('POST', '/v1/auth/login', [
            'email' => str_repeat('a', 257),
            'password' => str_repeat('a', 257),
        ]);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email may not be greater than 256 characters.',
                ],
                'password' => [
                    'The password may not be greater than 256 characters.',
                ]
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testEmailFormatIncorrect()
    {
        $response = $this->json('POST', '/v1/auth/login', [
            'email' => 'bryce',
            'password' => str_repeat('a', 257),
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

    public function testUserByEmailDoesNotExist()
    {
        $response = $this->json('POST', '/v1/auth/login', [
            'email' => 'guy@smiley.com',
            'password' => '123'
        ]);

        $response->assertJson([
            'message' => 'Invalid login credentials.'
        ]);
        $response->assertStatus(401);
    }

    public function testByEmailPasswordWrong()
    {
        factory(User::class)->create([
            'email' => 'guy@smiley.com',
            'password' => 'do not guess me!'
        ]);

        $response = $this->json('POST', '/v1/auth/login', [
            'email' => 'guy@smiley.com',
            'password' => '123'
        ]);
        $response->assertJson([
            'message' => 'Invalid login credentials.'
        ]);
        $response->assertStatus(401);
    }

    public function testByEmailSuccessLogin()
    {
        factory(User::class)->create([
            'email' => 'guy@smiley.com',
            'password' => Hash::make('complex!')
        ]);

        $response = $this->json('POST', '/v1/auth/login', [
            'email' => 'guy@smiley.com',
            'password' => 'complex!'
        ]);
        $response->assertJsonStructure([
            'token'
        ]);
        $response->assertStatus(200);
    }
}