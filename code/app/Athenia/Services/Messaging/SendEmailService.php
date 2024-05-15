<?php
declare(strict_types=1);

namespace App\Athenia\Services\Messaging;

use App\Athenia\Contracts\Models\Messaging\CanReceiveEmailsContract;
use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Services\Messaging\SendEmailServiceContract;
use App\Athenia\Mail\MessageMailer;
use App\Models\Messaging\Message;
use Illuminate\Contracts\Mail\Mailer;

class SendEmailService implements SendEmailServiceContract
{
    /**
     * @param Mailer $mailer
     */
    public function __construct(private Mailer $mailer) {}

    /**
     * Attempts to send a message to the receiver
     *
     * @param CanReceiveMessageContract $receiver
     * @param Message $message
     * @return bool
     */
    public function sendMessage(CanReceiveMessageContract $receiver, Message $message): bool
    {
        if ($receiver instanceof CanReceiveEmailsContract && $receiver->canReceive($message)) {
            $this->mailer->send(new MessageMailer($receiver, $message));

            return true;
        }

        return false;
    }
}