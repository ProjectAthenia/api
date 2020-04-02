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
 * Class UserThreadMessageIndexTest
 * @package Tests\Feature\User\Thread\Message
 */
class UserThreadMessageIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        User::unsetEventDispatcher();
    }

    public function testNotLoggedInUserBlocked()
    {
        $user = factory(User::class)->create();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'private_message',
        ]);

        $response = $this->json('GET', $this->path . $user->id . '/threads/' . $thread->id . '/messages');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $user = factory(User::class)->create();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'private_message',
        ]);

        $response = $this->json('GET', $this->path . $user->id . '/threads/' . $thread->id . '/messages');

        $response->assertStatus(403);
    }

    public function testUserNotPartOfThreadBlocked()
    {
        $this->actAsUser();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'private_message',
        ]);

        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages');

        $response->assertStatus(403);
    }

    public function testNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/5642/messages');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'private_message',
        ]);

        $thread->users()->sync([$this->actingAs->id]);

        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'private_message',
        ]);

        $thread->users()->sync([$this->actingAs->id]);

        factory(Message::class, 5)->create();
        factory(Message::class, 15)->create([
            'thread_id' => $thread->id,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 15,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 10,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Message())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages?page=2');
        $response->assertStatus(200);
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 10,
            'from' => 11,
            'to' => 15,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Message())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages?page=2&limit=5');
        $response->assertStatus(200);
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 5,
            'from' => 6,
            'to' => 10,
            'last_page' => 3
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Message())->toArray())
                ]
            ]);
    }

    public function testGetPaginationWithProperOrder()
    {
        $this->actAsUser();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'private_message',
        ]);

        $thread->users()->sync([$this->actingAs->id]);

        $message1 = factory(Message::class)->create([
            'thread_id' => $thread->id,
            'created_at' => '2018-10-10 12:00:00',
        ]);
        $message2 = factory(Message::class)->create([
            'thread_id' => $thread->id,
            'created_at' => '2018-11-10 12:00:00',
        ]);
        $message3 = factory(Message::class)->create([
            'thread_id' => $thread->id,
            'created_at' => '2017-10-10 12:00:00',
        ]);

        // ascending
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages?order[created_at]=asc');
        $response->assertStatus(200);

        $this->assertEquals($message3->id, $response->original[0]->id);
        $this->assertEquals($message1->id, $response->original[1]->id);
        $this->assertEquals($message2->id, $response->original[2]->id);

        // descending
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads/' . $thread->id . '/messages?order[created_at]=desc');
        $response->assertStatus(200);

        $this->assertEquals($message2->id, $response->original[0]->id);
        $this->assertEquals($message1->id, $response->original[1]->id);
        $this->assertEquals($message3->id, $response->original[2]->id);
    }
}