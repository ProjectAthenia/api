<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Authentication;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class RefreshTest
 * @package Tests\Feature\Http\Authentication
 */
class RefreshTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationLog();
        $this->setupDatabase();
    }

    public function testTokenRefresh()
    {
        factory(User::class)->create([
            'email' => 'test@test.com',
            'password' => Hash::make('complex!')
        ]);

        $loginResponse = $this->json('POST', '/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'complex!'
        ]);
        $loginResponse->assertJsonStructure([
            'token'
        ]);
        $loginResponse->assertStatus(200);

        $token = $loginResponse->original['token'];

        $response = $this->json('POST', '/v1/auth/refresh', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertJsonStructure(['token']);

        $token = $response->original['token'];

        $response = $this->json('GET', '/v1/users/me', [], [
            'Authorization' => $token,
        ]);

        $response->assertStatus(200);
    }

    public function testTokenRefreshAfterRefreshWindowFails()
    {
        factory(User::class)->create([
            'email' => 'test@test.com',
            'password' => Hash::make('complex!')
        ]);

        $loginResponse = $this->json('POST', '/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'complex!'
        ]);
        $loginResponse->assertJsonStructure([
            'token'
        ]);
        $loginResponse->assertStatus(200);

        $token = $loginResponse->original['token'];

        Carbon::setTestNow(Carbon::now()->addMonth(1)->addDay(1));
        $response = $this->json('POST', '/v1/auth/refresh', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => 'Token has expired and can no longer be refreshed'
        ]);
    }

    public function testTokenRefreshAfterExpirationBeforeRefreshTimeSucceeds()
    {
        factory(User::class)->create([
            'email' => 'test@test.com',
            'password' => Hash::make('complex!')
        ]);

        $loginResponse = $this->json('POST', '/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'complex!'
        ]);
        $loginResponse->assertJsonStructure([
            'token'
        ]);
        $loginResponse->assertStatus(200);

        $token = $loginResponse->original['token'];

        Carbon::setTestNow(Carbon::now()->addHours(2));
        $response = $this->json('POST', '/v1/auth/refresh', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertJsonStructure(['token']);

        $newToken = $response->original['token'];

        $this->assertNotEquals($token, $newToken);

        $response = $this->json('GET', '/v1/users/me', [], [
            'Authorization' => $newToken,
        ]);

        $response->assertStatus(200);
    }
}