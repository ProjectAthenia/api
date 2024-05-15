<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Messaging;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Events\Messaging\MessageCreatedEvent;
use App\Athenia\Events\Messaging\MessageSentEvent;
use App\Athenia\Mail\MessageMailer;
use App\Listeners\Messaging\MessageCreatedListener;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class MessageCreatedListenerTest
 * @package Tests\Unit\Listeners\Message
 */
final class MessageCreatedListenerTest extends TestCase
{
    public function testHandleViaEmail(): void
    {
        $mailer = mock(Mailer::class);
        $messageRepository = mock(MessageRepositoryContract::class);
        $events = mock(Dispatcher::class);
        $listener = new MessageCreatedListener(
            $mailer,
            mock(Client::class),
            $messageRepository,
            $events,
            mock(Repository::class)
        );

        $message = new Message([
            'via' => [
                Message::VIA_EMAIL,
            ],
            'to' => new User(),
        ]);
        $event = new MessageCreatedEvent($message);

        $carbon = new Carbon();
        Carbon::setTestNow($carbon);

        $messageRepository->shouldReceive('update')->once()->with($message, ['scheduled_at' => $carbon]);
        $mailer->shouldReceive('send')->once()->with(\Mockery::on(function (MessageMailer $mailer) {

            return true;
        }));

        $listener->handle($event);
    }

    public function testHandleViaPushToUser(): void
    {
        $client = mock(Client::class)->shouldAllowMockingMethod('post');
        $messageRepository = mock(MessageRepositoryContract::class);
        $events = mock(Dispatcher::class);
        $config = mock(Repository::class);
        $listener = new MessageCreatedListener(
            mock(Mailer::class),
            $client,
            $messageRepository,
            $events,
            $config
        );

        $message = new Message([
            'via' => [
                Message::VIA_PUSH_NOTIFICATION,
            ],
            'to' => new User([
                'push_notification_key' => 'a key',
                'allow_users_to_add_me' => true,
                'receive_push_notifications' => true,
            ]),
            'data' => [
            ],
        ]);
        $event = new MessageCreatedEvent($message);

        $carbon = new Carbon();
        Carbon::setTestNow($carbon);

        $messageRepository->shouldReceive('update')->once()->with($message, ['scheduled_at' => $carbon]);
        $client->shouldReceive('post')->once();

        $config->shouldReceive('get')->once()->with('services.fcm.key')->andReturn('');

        $events->shouldReceive('dispatch')->once()->with(\Mockery::on(function(MessageSentEvent $event) {
            return true;
        }));

        $listener->handle($event);
    }

    public function testHandleViaPushToThread(): void
    {
        $client = mock(Client::class)->shouldAllowMockingMethod('post');
        $messageRepository = mock(MessageRepositoryContract::class);
        $events = mock(Dispatcher::class);
        $config = mock(Repository::class);
        $listener = new MessageCreatedListener(
            mock(Mailer::class),
            $client,
            $messageRepository,
            $events,
            $config
        );

        $message = new Message([
            'via' => [
                Message::VIA_PUSH_NOTIFICATION,
            ],
            'from_id' => 3453,
            'thread' => new Thread([
                'users' => new Collection([
                    new User([
                        'push_notification_key' => 'a key',
                        'allow_users_to_add_me' => true,
                        'receive_push_notifications' => true,
                    ]),
                ]),
            ]),
            'data' => [
            ],
        ]);
        $event = new MessageCreatedEvent($message);

        $carbon = new Carbon();
        Carbon::setTestNow($carbon);

        $messageRepository->shouldReceive('update')->once()->with($message, ['scheduled_at' => $carbon]);
        $client->shouldReceive('post')->once();

        $config->shouldReceive('get')->once()->with('services.fcm.key')->andReturn('');

        $events->shouldReceive('dispatch')->once()->with(\Mockery::on(function(MessageSentEvent $event) {
            return true;
        }));

        $listener->handle($event);
    }

    public function testHandleViaPushDoesNotSendDueToSettings(): void
    {
        $messageRepository = mock(MessageRepositoryContract::class);
        $events = mock(Dispatcher::class);
        $config = mock(Repository::class);
        $listener = new MessageCreatedListener(
            mock(Mailer::class),
            mock(Client::class),
            $messageRepository,
            $events,
            $config
        );

        $message = new Message([
            'via' => [
                Message::VIA_PUSH_NOTIFICATION,
            ],
            'to' => new User([
                'push_notification_key' => 'a key',
                'receive_push_notifications' => false,
            ])
        ]);
        $event = new MessageCreatedEvent($message);

        $carbon = new Carbon();
        Carbon::setTestNow($carbon);

        $messageRepository->shouldReceive('update')->once()->with($message, ['scheduled_at' => $carbon]);

        $events->shouldReceive('dispatch')->once()->with(\Mockery::on(function(MessageSentEvent $event) {
            return true;
        }));

        $listener->handle($event);
    }
}