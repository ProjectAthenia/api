<?php
declare(strict_types=1);

namespace Tests\Feature\User\Thread;

use App\Models\User\Message;
use App\Models\User\Thread;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserThreadIndexTest
 * @package Tests\Feature\User\Thread
 */
class UserThreadIndexTest extends TestCase
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

        $response = $this->json('GET', $this->path . $user->id . '/threads');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $user = factory(User::class)->create();

        $response = $this->json('GET', $this->path . $user->id . '/threads');

        $response->assertStatus(403);
    }

    public function testUserNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/threads');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path. $this->actingAs->id . '/threads?subject_type=private_message');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();

        factory(Thread::class, 5)->create([
            'subject_type' => 'private_message'
        ]);
        $threads = factory(Thread::class, 15)->create([
            'subject_type' => 'private_message'
        ]);

        /** @var Thread $thread */
        foreach ($threads as $thread) {
            $thread->users()->sync([$this->actingAs->id]);
            factory(Message::class)->create([
                'thread_id' => $thread->id,
            ]);
        }

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads?subject_type=private_message');
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
                    '*' =>  array_keys((new Thread())->toArray())
                ]
            ]);
        $this->assertNotNull($response->original[0]['last_message']);

        // second page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads?page=2&subject_type=private_message');
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
                    '*' =>  array_keys((new Thread())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads?page=2&limit=5&subject_type=private_message');
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
                    '*' =>  array_keys((new Thread())->toArray())
                ]
            ]);
    }

    public function testGetPaginationWithExpand()
    {
        $this->actAsUser();

        factory(Thread::class, 5)->create([
            'subject_type' => 'private_message'
        ]);
        $threads = factory(Thread::class, 15)->create([
            'subject_type' => 'private_message'
        ]);

        /** @var Thread $thread */
        foreach ($threads as $thread) {
            $thread->users()->sync([$this->actingAs->id]);
            factory(Message::class)->create([
                'thread_id' => $thread->id,
            ]);
        }

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/threads?expand[users]=*&subject_type=private_message');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 15,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 10,
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' => array_keys((new Thread())->toArray())
                ]
            ]);
    }
}