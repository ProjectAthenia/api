<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\Messaging;

use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Models\Messaging\HasMessageReceiversContract;
use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Contracts\Services\Messaging\MessageSendingSelectionServiceContract;
use App\Athenia\Events\Messaging\MessageCreatedEvent;
use App\Athenia\Events\Messaging\MessageSentEvent;
use App\Models\Messaging\Message;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class MessageCreatedListener
 * @package App\Listeners\Message
 */
class MessageCreatedListener implements ShouldQueue
{
    /**
     * MessageCreatedListener constructor.
     * @param MessageSendingSelectionServiceContract $messageSendingSelectionService
     * @param MessageRepositoryContract $messageRepository
     */
    public function __construct(
        private MessageSendingSelectionServiceContract $messageSendingSelectionService,
        private MessageRepositoryContract $messageRepository,
        private Dispatcher $events,
    ) {}

    /**
     * Schedules the message to be sent
     *
     * @param MessageCreatedEvent $event
     * @throws Exception
     */
    public function handle(MessageCreatedEvent $event): void
    {
        $message = $event->getMessage();

        $this->messageRepository->update($message, [
            'scheduled_at' => Carbon::now(),
        ]);

        $channels = $message->via ?? [Message::VIA_EMAIL];

        $sent = false;

        foreach ($channels as $channel) {
            $service = $this->messageSendingSelectionService->getSendingService($channel);

            $to = $message->to;
            if ($to instanceof CanReceiveMessageContract) {
                $service->sendMessage($to, $message);
                $sent = true;
            }
            if ($to instanceof HasMessageReceiversContract) {
                foreach ($to->messageReceivers as $messageReceiver) {
                    $service->sendMessage($messageReceiver, $message);
                    $sent = true;
                }
            }
        }

        if ($sent) {
            $this->events->dispatch(new MessageSentEvent($message));
        }
    }
}
