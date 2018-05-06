<?php
declare(strict_types=1);

namespace App\Listeners\Message;

use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Events\Message\MessageSentEvent;
use Carbon\Carbon;

/**
 * Class MessageSentListener
 * @package App\Listeners\Message
 */
class MessageSentListener
{
    /**
     * @var MessageRepositoryContract
     */
    private $messageRepository;

    /**
     * MessageSentListener constructor.
     * @param MessageRepositoryContract $messageRepository
     */
    public function __construct(MessageRepositoryContract $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Sets the sent at field to the message
     *
     * @param MessageSentEvent $event
     */
    public function handle(MessageSentEvent $event)
    {
        $message = $event->getMessage();

        $this->messageRepository->update($message, [
            'sent_at' => Carbon::now(),
        ]);
    }
}