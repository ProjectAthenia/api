<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\ForgotPassword;

use App\Athenia\Events\User\ForgotPasswordEvent;
use App\Models\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ForgotPasswordTest
 * @package Tests\Athenia\Feature\Http\ForgotPassword
 */
final class ForgotPasswordTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    private $route = '/v1/forgot-password';

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
        $this->setupDatabase();
    }

    public function testMissingRequiredFields(): void
    {
        $response = $this->json('POST', $this->route);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email field is required.',
                ],
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testStringFieldsTooLong(): void
    {
        $response = $this->json('POST', $this->route, [
            'email' => str_repeat('a', 121),
        ]);

        $response->assertJson([
            'errors' => [
                'email' => [
                    'The email may not be greater than 120 characters.',
                ],
            ]
        ]);
        $response->assertStatus(400);
    }

    public function testEmailFormatIncorrect(): void
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

    public function testUserByEmailDoesNotExist(): void
    {
        $response = $this->json('POST', $this->route, [
            'email' => 'guy@smiley.com',
        ]);

        $response->assertJson([
            'errors' => [
                'email' => ['The selected email is invalid.'],
            ],
        ]);
        $response->assertStatus(400);
    }

    public function testSuccess(): void
    {
        $user = User::factory()->create([
            'email' => 'test@test.com'
        ]);

        $dispatcher = mock(Dispatcher::class);

        $forgotPasswordEventDispatched = false;

        $dispatcher->shouldReceive('dispatch')
            ->with(\Mockery::on(function ($event) use ($user, &$forgotPasswordEventDispatched) {

                if ($event instanceof ForgotPasswordEvent) {

                    $token = $event->getPasswordToken();

                    $this->assertEquals($user->id, $token->user_id);

                    $forgotPasswordEventDispatched = true;

                    return true;
                }

                return true;
            })
        );

        $this->app->bind(Dispatcher::class, function () use ($dispatcher) {
            return $dispatcher;
        });

        $response = $this->json('POST', $this->route, [
            'email' => 'test@test.com',
        ]);

        $this->assertTrue($forgotPasswordEventDispatched);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK'
        ]);
    }
}
