<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Messaging;

use App\Models\Messaging\Message;

interface SendEmailServiceContract extends BaseMessageSendingServiceContract
{
    /**
     * Used when there is no to set on the message
     *
     * @param Message $message
     * @return void
     */
    public function sendDirectMessage(Message $message);
}