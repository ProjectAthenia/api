<?php
declare(strict_types=1);

namespace App\Athenia\Services\Messaging;

use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Services\Messaging\BaseMessageSendingServiceContract;
use App\Athenia\Exceptions\NotImplementedException;
use App\Models\Messaging\Message;

class MessageSendingServiceNotImplemented implements BaseMessageSendingServiceContract
{
    /**
     * Attempts to send a message to the receiver
     *
     * @param CanReceiveMessageContract $receiver
     * @param Message $message
     * @return bool
     */
    public function sendMessage(CanReceiveMessageContract $receiver, Message $message): bool
    {
        throw new NotImplementedException('This messaging channel is not currently implemented. There probably needs to be additional configuration to use this channel.');
    }
}