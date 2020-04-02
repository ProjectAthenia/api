<?php
declare(strict_types=1);

namespace Tests\Feature\User\Thread;

use App\Models\User\Thread;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserThreadCreateTest
 * @package Tests\Feature\User\Thread
 */
class UserThreadCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        $this->user = factory(User::class)->create();

        $this->path.= $this->user->id . '/threads';
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actingAs($this->user);

        $otherUser = factory(User::class)->create();
        /** @var Thread $thread */
        $thread = factory(Thread::class)->create();
        $thread->users()->attach($otherUser->id);
        $thread->users()->attach($this->user->id);

        $user = factory(User::class)->create();

        $response = $this->json('POST', $this->path, [
            'subject_type' => 'private_message',
            'users' => [$user->id],
        ]);

        $response->assertStatus(201);

        /** @var Thread $thread */
        $thread = Thread::all()[1];
        $this->assertCount(2, $thread->users);

        $this->assertTrue($thread->users->contains($this->user->id));
        $this->assertTrue($thread->users->contains($user->id));
    }

    public function testCreateInvalidArrayFields()
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'subject_type' => 'private_message',
            'users' => 'hi',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'users' => ['The users must be an array.'],
            ],
        ]);
    }

    public function testCreateInvalidIntegerFields()
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'subject_type' => 'private_message',
            'subject_id' => 'hi',
            'users' => ['hi'],
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'subject_id' => ['The subject id must be an integer.'],
                'users.0' => ['The users.0 must be an integer.'],
            ],
        ]);
    }

    public function testCreateInvalidModelFields()
    {
        $this->actingAs($this->user);

        $response = $this->json('POST', $this->path, [
            'subject_type' => 'private_message',
            'users' => [546],
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'users.0' => ['The selected users.0 is invalid.'],
            ],
        ]);
    }
}