<?php
declare(strict_types=1);

namespace App\Services\Messaging;

use App\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Contracts\Services\Messaging\BaseMessageSendingServiceContract;
use App\Exceptions\NotImplementedException;
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