<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Messaging;

use App\Contracts\Models\CanReceiveTextMessagesContract;
use App\Events\Messaging\MessageCreatedEvent;
use App\Exceptions\NotImplementedException;
use App\Models\Messaging\Message;
use App\Models\Role;
use App\Models\User\User;
use App\Repositories\Messaging\MessageRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class MessageRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
class MessageRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var MessageRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new MessageRepository(
            new Message(),
            $this->getGenericLogMock(),
            new UserRepository(
                new User(),
                $this->getGenericLogMock(),
                mock(Hasher::class),
                $this->app->make('config'),
            ),
        );
    }

    public function testFindAllSuccess(): void
    {
        foreach (Message::all() as $resource) {
            $resource->delete();
        }

        Message::factory()->count( 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        foreach (Message::all() as $resource) {
            $resource->delete();
        }

        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testCreateSuccess(): void
    {
        $user = User::factory()->create();

        $dispatcher = mock(Dispatcher::class);

        $dispatcher->shouldReceive('until');
        $dispatcher->shouldReceive('dispatch')
            ->with(\Mockery::on(function (String $eventName) {
                return true;
            }),
            \Mockery::on(function (Message $message) {
                return true;
            })
        );
        $dispatcher->shouldReceive('dispatch')->once()
            ->with(\Mockery::on(function (MessageCreatedEvent $event) {
                return true;
            })
        );

        Message::setEventDispatcher($dispatcher);

        /** @var Message $message */
        $message = $this->repository->create([
            'subject' => 'Hello',
            'template' => 'test_template',
            'email' => 'test@test.com',
            'to_id' => $user->id,
            'to_type' => 'user',
            'data' => ['greeting' => 'hello'],
        ]);


        $this->assertEquals('Hello', $message->subject);
        $this->assertEquals('test_template', $message->template);
        $this->assertEquals('test@test.com', $message->email);
        $this->assertEquals(['greeting' => 'hello'], $message->data);
        $this->assertEquals($user->id, $message->to_id);
    }

    public function testDeleteThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new Message());
    }

    public function testFindOrFailSuccess(): void
    {
        $model = Message::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Message::factory()->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testUpdateSuccess(): void
    {
        $dispatcher = mock(Dispatcher::class);
        $dispatcher->shouldReceive('until');
        $dispatcher->shouldReceive('dispatch');
        Message::setEventDispatcher($dispatcher);

        $message = Message::factory()->create();

        /** @var Message $result */
        $result = $this->repository->update($message, [
            'scheduled_at' => '2018-05-13 00:00:00',
            'sent_at' => '2018-05-13 00:00:02',
        ]);

        $this->assertEquals('2018-05-13 00:00:00', $result->scheduled_at->toDateTimeString());
        $this->assertEquals('2018-05-13 00:00:02', $result->sent_at->toDateTimeString());
    }

    public function testSendEmailToUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $dispatcher = mock(Dispatcher::class);

        $dispatcher->shouldAllowMockingMethod('fire');

        $dispatcher->shouldReceive('until');
        $dispatcher->shouldReceive('dispatch')
            ->with(\Mockery::on(function (String $eventName) {
                return true;
            }),
                \Mockery::on(function (Message $message) {
                    return true;
                })
            );
        $dispatcher->shouldReceive('dispatch')->once()
            ->with(\Mockery::on(function (MessageCreatedEvent $event) {
                return true;
            })
        );

        Message::setEventDispatcher($dispatcher);

        $result = $this->repository->sendEmailToUser($user, 'A Subject', 'template', ['yes' => 'no']);

        $this->assertEquals('A Subject', $result->subject);
        $this->assertEquals('template', $result->template);
        $this->assertEquals($user->email, $result->email);
        $this->assertEquals('no', $result->data['yes']);
        $this->assertNotNull($result->data['greeting']);
    }

    public function testSendEmailToUserWithGreetingOverride(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $dispatcher = mock(Dispatcher::class);

        $dispatcher->shouldAllowMockingMethod('fire');

        $dispatcher->shouldReceive('until');
        $dispatcher->shouldReceive('dispatch')
            ->with(\Mockery::on(function (String $eventName) {
                return true;
            }),
                \Mockery::on(function (Message $message) {
                    return true;
                })
            );
        $dispatcher->shouldReceive('dispatch')->once()
            ->with(\Mockery::on(function (MessageCreatedEvent $event) {
                return true;
            })
        );

        Message::setEventDispatcher($dispatcher);

        $result = $this->repository->sendEmailToUser($user, 'A Subject', 'template', ['yes' => 'no'], 'To whom it may concern,');

        $this->assertEquals('A Subject', $result->subject);
        $this->assertEquals('template', $result->template);
        $this->assertEquals($user->email, $result->email);
        $this->assertEquals('no', $result->data['yes']);
        $this->assertNotNull($result->data['greeting']);
        $this->assertEquals('To whom it may concern,', $result->data['greeting']);
    }

    public function testSendEmailToSuperAdmins(): void
    {
        Message::unsetEventDispatcher();

        $user1 = User::factory()->create([
            'email' => 'test@test.com',
            'first_name' => 'System User'
        ]);
        $user1->roles()->attach(Role::SUPER_ADMIN);
        $user2 = User::factory()->create([
            'email' => 'test@test.com',
            'first_name' => 'System User'
        ]);
        $user2->roles()->attach(Role::SUPER_ADMIN);

        User::factory()->count( 3)->create();

        $result = $this->repository->sendEmailToSuperAdmins('A Subject', '');

        $this->assertCount(2, $result);
        $this->assertContains($user1->id, $result->pluck('to_id'));
        $this->assertContains($user2->id, $result->pluck('to_id'));
    }

    public function testSendTextMessage(): void
    {
        $textMessageReceiver = mock(CanReceiveTextMessagesContract::class);
        $textMessageReceiver->id = 234;
        $textMessageReceiver->shouldReceive('morphRelationName')->andReturn('text_message');
        $result = $this->repository->sendTextMessage($textMessageReceiver, 'hello');

        $this->assertEquals(['message' => 'hello'], $result->data);
    }
}
