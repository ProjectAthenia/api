<?php
declare(strict_types=1);

namespace App\Contracts\Services\Messaging;

use App\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Models\Messaging\Message;

interface BaseMessageSendingServiceContract
{
    /**
     * Attempts to send a message to the receiver
     *
     * @param CanReceiveMessageContract $receiver
     * @param Message $message
     * @return bool
     */
    public function sendMessage(CanReceiveMessageContract $receiver, Message $message): bool;
}