<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\User;

use App\Events\Message\MessageCreatedEvent;
use App\Exceptions\NotImplementedException;
use App\Models\User\Message;
use App\Models\User\User;
use App\Repositories\User\MessageRepository;
use Illuminate\Contracts\Events\Dispatcher;
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

        $this->repository = new MessageRepository(new Message(), $this->getGenericLogMock());
    }

    public function testFindAllSuccess()
    {
        foreach (Message::all() as $resource) {
            $resource->delete();
        }

        factory(Message::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        foreach (Message::all() as $resource) {
            $resource->delete();
        }

        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testCreateSuccess()
    {
        $user = factory(User::class)->create();

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
            'data' => ['greeting' => 'hello'],
        ], $user);


        $this->assertEquals('Hello', $message->subject);
        $this->assertEquals('test_template', $message->template);
        $this->assertEquals('test@test.com', $message->email);
        $this->assertEquals(['greeting' => 'hello'], $message->data);
        $this->assertEquals($user->id, $message->to_id);
    }

    public function testDeleteThrowsException()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new Message());
    }

    public function testFindOrFailThrowsException()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->findOrFail(1);
    }

    public function testUpdateSuccess()
    {
        $dispatcher = mock(Dispatcher::class);
        $dispatcher->shouldReceive('until');
        $dispatcher->shouldReceive('dispatch');
        Message::setEventDispatcher($dispatcher);

        $message = factory(Message::class)->create();

        /** @var Message $result */
        $result = $this->repository->update($message, [
            'scheduled_at' => '2018-05-13 00:00:00',
            'sent_at' => '2018-05-13 00:00:02',
        ]);

        $this->assertEquals('2018-05-13 00:00:00', $result->scheduled_at->toDateTimeString());
        $this->assertEquals('2018-05-13 00:00:02', $result->sent_at->toDateTimeString());
    }

    public function testSendEmailToUser()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

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

    public function testSendEmailToUserWithGreetingOverride()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

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
}