<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Messaging;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Contracts\Services\Messaging\MessageSendingSelectionServiceContract;
use App\Athenia\Contracts\Services\Messaging\SendSlackNotificationServiceContract;
use App\Athenia\Events\Messaging\MessageCreatedEvent;
use App\Athenia\Events\Messaging\MessageSentEvent;
use App\Athenia\Listeners\Messaging\MessageCreatedListener;
use App\Athenia\Mail\MessageMailer;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\User\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class MessageCreatedListenerTest
 * @package Tests\Athenia\Unit\Listeners\Message
 */
final class MessageCreatedListenerTest extends TestCase
{
    /**
     * @var MessageSendingSelectionServiceContract|(MessageSendingSelectionServiceContract&MockInterface&LegacyMockInterface)|(MessageSendingSelectionServiceContract&CustomMockInterface)|array|(MockInterface&LegacyMockInterface)|CustomMockInterface
     */
    private $messageSendingSelectionService;

    /**
     * @var MessageRepositoryContract|(MessageRepositoryContract&MockInterface&LegacyMockInterface)|(MessageRepositoryContract&CustomMockInterface)|array|(MockInterface&LegacyMockInterface)|CustomMockInterface
     */
    private $messageRepository;

    /**
     * @var array|Dispatcher|(Dispatcher&MockInterface&LegacyMockInterface)|(Dispatcher&CustomMockInterface)|(MockInterface&LegacyMockInterface)|CustomMockInterface
     */
    private $events;

    /**
     * @var MessageCreatedListener
     */
    private MessageCreatedListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageSendingSelectionService = mock(MessageSendingSelectionServiceContract::class);
        $this->messageRepository = mock(MessageRepositoryContract::class);
        $this->events = mock(Dispatcher::class);
        $this->listener = new MessageCreatedListener(
            $this->messageSendingSelectionService,
            $this->messageRepository,
            $this->events,
        );
    }

    public function testHandleDoesNothingWhenSendingServiceIsNotConfigured()
    {
        $message = new Message([
            'via' => Message::VIA_SLACK,
        ]);
        $event = new MessageCreatedEvent($message);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->messageRepository->shouldReceive('update')->with($message, [
            'scheduled_at' => $now,
        ]);
        $this->messageSendingSelectionService
            ->shouldReceive('getSendingService')
            ->with(Message::VIA_SLACK)
            ->andReturn(null);

        $this->listener->handle($event);
    }

    public function testHandleDoesNothingWithNoValidSenders()
    {
        $message = new Message([
            'via' => Message::VIA_SLACK,
        ]);
        $event = new MessageCreatedEvent($message);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $slackService = mock(SendSlackNotificationServiceContract::class);

        $this->messageRepository->shouldReceive('update')->with($message, [
            'scheduled_at' => $now,
        ]);
        $this->messageSendingSelectionService
            ->shouldReceive('getSendingService')
            ->with(Message::VIA_SLACK)
            ->andReturn($slackService);

        $this->listener->handle($event);
    }

    public function testHandleSendsToSingleReceiver()
    {
        $message = new Message([
            'via' => Message::VIA_SLACK,
            'to' => new User()
        ]);
        $event = new MessageCreatedEvent($message);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $slackService = mock(SendSlackNotificationServiceContract::class);
        $slackService->shouldReceive('sendMessage')->with($message->to, $message);

        $this->messageRepository->shouldReceive('update')->with($message, [
            'scheduled_at' => $now,
        ]);
        $this->messageSendingSelectionService
            ->shouldReceive('getSendingService')
            ->with(Message::VIA_SLACK)
            ->andReturn($slackService);
        $this->events->shouldReceive('dispatch');

        $this->listener->handle($event);
    }

    public function testHandleSendsToChildReceivers()
    {
        $message = new Message([
            'via' => Message::VIA_SLACK,
            'to' => new Organization([
                'organizationManagers' => collect([
                    new OrganizationManager([
                        'user' => new User([
                            'id' => 43,
                        ]),
                    ]),
                    new OrganizationManager([
                        'user' => new User([
                            'id' => 7,
                        ]),
                    ]),
                ])
            ])
        ]);
        $event = new MessageCreatedEvent($message);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $slackService = mock(SendSlackNotificationServiceContract::class);
        $slackService->shouldReceive('sendMessage')
            ->with($message->to, $message);
        $slackService->shouldReceive('sendMessage')
            ->with($message->to->organizationManagers[0]->user, $message);
        $slackService->shouldReceive('sendMessage')
            ->with($message->to->organizationManagers[1]->user, $message);

        $this->messageRepository->shouldReceive('update')->with($message, [
            'scheduled_at' => $now,
        ]);
        $this->messageSendingSelectionService
            ->shouldReceive('getSendingService')
            ->with(Message::VIA_SLACK)
            ->andReturn($slackService);
        $this->events->shouldReceive('dispatch');

        $this->listener->handle($event);
    }
}