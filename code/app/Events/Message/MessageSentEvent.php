<?php
declare(strict_types=1);

namespace App\Events\Message;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User\Message;

/**
 * Class MessageSentEvent
 * @package App\Events\Message
 */
class MessageSentEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @var Message
     */
    private $message;

    /**
     * MessageSentEvent constructor.
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