<?php
declare(strict_types=1);

namespace App\Athenia\Events\Messaging;

use App\Models\Messaging\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class MessageCreatedEvent
 * @package App\Events\Message
 */
class MessageCreatedEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @var Message
     */
    private $message;

    /**
     * MessageCreatedEvent constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}