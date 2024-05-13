<?php
declare(strict_types=1);

namespace App\Services\Messaging;

use App\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Contracts\Models\Messaging\CanReceiveSMSContract;
use App\Contracts\Services\Messaging\SendSMSServiceContract;
use App\Models\Messaging\Message;
use NotificationChannels\Twilio\Twilio;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Psr\Log\LoggerInterface;

class SendSMSNotificationService implements SendSMSServiceContract
{
    public function __construct(private Twilio $twilio, private LoggerInterface $log)
    {
    }

    /**
     * Attempts to send a message to the receiver
     *
     * @param CanReceiveMessageContract $receiver
     * @param Message $message
     * @return bool
     */
    public function sendMessage(CanReceiveMessageContract $receiver, Message $message): bool
    {
        if ($receiver instanceof CanReceiveSMSContract && $receiver->canReceive($message)) {

            $sms = new TwilioSmsMessage($message->data['message']);
            try {
                $this->twilio->sendMessage($sms, $receiver->getPhoneNumber());
                return true;
            } catch (\Exception $e) {

            }
        }

        return false;
    }
}