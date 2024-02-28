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
 * Class UserThreadMessageUpdateTest
 * @package Tests\Feature\User\Thread\Message
 */
class UserThreadMessageUpdateTest extends TestCase
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
            'subject_type' => 'private_message',
        ]);

        $this->path.= $this->user->id . '/threads/' . $this->thread->id . '/messages/';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        Message::unsetEventDispatcher();
        $message = Message::factory()->create();
        $response = $this->json('PUT', $this->path . $message->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful(): void
    {
        Message::unsetEventDispatcher();
        $this->actingAs($this->user);

        $this->thread->users()->sync([$this->user->id]);
        $message = Message::factory()->create([
            'to_id' => $this->user->id,
            'thread_id' => $this->thread->id,
        ]);

        Message::flushEventListeners();

        $response = $this->json('PUT', $this->path . $message->id, [
            'seen' => true,
        ]);

        $response->assertStatus(200);

        /** @var Message $message */
        $message = Message::first();

        $this->assertNotNull($message->seen_at);
    }

    public function testUpdateFailsInvalidBooleanFields(): void
    {
        Message::unsetEventDispatcher();
        $this->actingAs($this->user);

        $this->thread->users()->sync([$this->user->id]);

        $message = Message::factory()->create([
            'to_id' => $this->user->id,
            'thread_id' => $this->thread->id,
        ]);

        $response = $this->json('PUT', $this->path . $message->id, [
            'seen' => 'hi',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'seen' => ['The seen field must be true or false.'],
            ],
        ]);
    }
}
