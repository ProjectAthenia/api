<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User;

use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserUpdateTest
 * @package Tests\Feature\Http\User
 */
class UserUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $character = factory(User::class)->create();
        $response = $this->json('PUT', $this->path . '/' . $character->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserBlocked()
    {
        $this->actAsUser();
        $user = factory(User::class)->create();

        $response = $this->json('PUT', $this->path . '/' . $user->id);

        $response->assertStatus(403);
    }

    public function testNotFound()
    {
        $this->actAsUser();

        $response = $this->json('PUT', $this->path . '/1');

        $response->assertStatus(404);
    }

    public function testUpdateSuccessful()
    {
        $user = factory(User::class)->create([
            'allow_users_to_add_me' => true,
            'receive_push_notifications' => true,
            'push_notification_key' => 'a key'
        ]);
        $this->actingAs($user);

        $data = [
            'email' => 'test@test.com',
            'allow_users_to_add_me' => false,
            'receive_push_notifications' => false,
        ];

        $response = $this->json('PUT', $this->path . '/' . $user->id, $data);

        $response->assertStatus(200);

        /** @var User $updated */
        $updated = User::find($user->id);

        $this->assertEquals('test@test.com', $updated->email);
        $this->assertNotTrue($updated->allow_users_to_add_me);
        $this->assertNotTrue($updated->receive_push_notifications);
        $this->assertEquals('a key', $updated->push_notification_key);
    }

    public function testUpdatePasswordSuccessful()
    {
        $this->actAsUser([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'password' => 'superSecret',
        ];

        $response = $this->json('PUT', $this->path . '/' . $this->actingAs->id, $data);

        $response->assertStatus(200);

        $loginResponse = $this->json('POST', '/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'superSecret',
        ]);

        $loginResponse->assertStatus(200);
    }

    public function testUpdateFailsInvalidBooleanFields()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->json('PUT', $this->path . '/' . $user->id, [
            'allow_users_to_add_me' => -1,
            'receive_push_notifications' => -1,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'allow_users_to_add_me' => ['The allow users to add me field must be true or false.'],
                'receive_push_notifications' => ['The receive push notifications field must be true or false.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidStringFields()
    {
        $this->actAsUser();

        $response = $this->json('PUT', $this->path . '/' . $this->actingAs->id, [
            'email' => 1,
            'name' => 1,
            'about_me' => 1,
            'password' => 1,
            'push_notification_key' => 1,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'email' => ['The email must be a string.'],
                'name' => ['The name must be a string.'],
                'about_me' => ['The about me must be a string.'],
                'password' => ['The password must be a string.'],
                'push_notification_key' => ['The push notification key must be a string.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidEmail()
    {
        $this->actAsUser();

        $response = $this->json('PUT', $this->path . '/' . $this->actingAs->id, [
            'email' => 'owriowf',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'email' => ['The email must be a valid email address.'],
            ]
        ]);
    }

    public function testUpdateFailsStringLengthWrong()
    {
        $this->actAsUser();

        $response = $this->json('PUT', $this->path . '/' . $this->actingAs->id, [
            'password' => str_repeat('a', 5),
            'email' => str_repeat('a', 121),
            'name' => str_repeat('a', 121),
            'push_notification_key' => str_repeat('a', 513),
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'password' => ['The password must be at least 6 characters.'],
                'email' => ['The email may not be greater than 120 characters.'],
                'name' => ['The name may not be greater than 120 characters.'],
                'push_notification_key' => ['The push notification key may not be greater than 512 characters.'],
            ]
        ]);
    }
}