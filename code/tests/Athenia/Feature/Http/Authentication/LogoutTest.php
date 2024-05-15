<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Authentication;

use App\Athenia\Http\Middleware\LogMiddleware;
use App\Models\User\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class LogoutTest
 * @package Tests\Feature\Http\Authentication
 */
final class LogoutTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
        $this->setupDatabase();
    }

    public function testLogout(): void
    {
        $this->app['env'] = 'testing-override';  // @todo fix this
        $this->app->instance(LogMiddleware::class, new class {
            public function handle($request, $next) {
                return $next($request);
            }
        });

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->json('POST', '/v1/auth/logout', [], ['Authorization' => 'Bearer ' . $token]);
        $this->app['env'] = 'testing'; // @todo resolve
        $response->assertStatus(200);
        JWTAuth::authenticate($token);
    }
}
