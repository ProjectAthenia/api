<?php
declare(strict_types=1);

namespace Tests\Feature\User\Thread\Message;

use App\Models\User\Message;
use App\Models\User\Thread;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserThreadMessageCreateTest
 * @package Tests\Feature\User\Thread\Message
 */
class UserThreadMessageCreateTest extends TestCase
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

    /**
     * @var Thread
     */
    private $thread;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        $this->user = User::factory()->create();
        $this->thread = Thread::factory()->create([
            'subject_type' => 'private_message'
        ]);

        $this->path.= $this->user->id . '/threads/' . $this->thread->id . '/messages';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful(): void
    {
        $this->actingAs($this->user);

        $this->thread->users()->sync([$this->user->id]);

        Message::flushEventListeners();

        $response = $this->json('POST', $this->path, [
            'message' => 'A Message',
        ]);

        $response->assertStatus(201);

        /** @var Message $message */
        $message = Message::first();

        $this->assertEquals('A Message', $message->data['body']);
        $this->assertEquals($this->user->id, $message->from_id);
    }

    public function testCreateMissingRequiredFields(): void
    {
        $this->actingAs($this->user);

        $this->thread->users()->sync([$this->user->id]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'message' => ['The message field is required.']
            ],
        ]);
    }

    public function testCreateInvalidStringFields(): void
    {
        $this->actingAs($this->user);

        $this->thread->users()->sync([$this->user->id]);

        $response = $this->json('POST', $this->path, [
            'message' => 3543,
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'message' => ['The message must be a string.'],
            ],
        ]);
    }
}
