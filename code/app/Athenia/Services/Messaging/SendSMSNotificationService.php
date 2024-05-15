<?php
declare(strict_types=1);

namespace App\Athenia\Services\Messaging;

use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Models\Messaging\CanReceiveSMSContract;
use App\Athenia\Contracts\Services\Messaging\SendSMSServiceContract;
use App\Models\Messaging\Message;
use NotificationChannels\Twilio\Twilio;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Psr\Log\LoggerInterface;

class SendSMSNotificationService implements SendSMSServiceContract
{
    /**
     * @param Twilio $twilio
     * @param LoggerInterface $logger
     */
    public function __construct(private Twilio $twilio, private LoggerInterface $logger)
    {}

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
                $error = 'Failed Sending SMS - ' . $e->getMessage();
                $this->logger->error($error, $e->getTrace());
                return false;
            }
        }

        return false;
    }
}