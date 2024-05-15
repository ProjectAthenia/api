<?php
declare(strict_types=1);

namespace App\Athenia\Services\Messaging;

use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Models\Messaging\CanReceivePushNotificationContract;
use App\Athenia\Contracts\Services\Messaging\SendPushNotificationServiceContract;
use App\Models\Messaging\Message;
use App\Models\Messaging\PushNotificationKey;
use Benwilkins\FCM\FcmChannel;
use Benwilkins\FCM\FcmMessage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class SendPushNotificationService implements SendPushNotificationServiceContract
{
    public function __construct(
        private string $fcmKey,
        private Client $client,
        private LoggerInterface $logger,
    ) {}

    /**
     * Attempts to send a message to the receiver
     *
     * @param CanReceiveMessageContract $receiver
     * @param Message $message
     * @return bool
     */
    public function sendMessage(CanReceiveMessageContract $receiver, Message $message): bool
    {
        if ($receiver instanceof CanReceivePushNotificationContract && $receiver->canReceive($message)) {

            $pushNotification = $this->formatPushNotification($message);

            $sent = false;
            foreach ($receiver->pushNotificationKeys as $pushNotificationKey) {
                $sent = $this->sendPushNotification($pushNotification, $pushNotificationKey) ? true : $sent;
            }

            return $sent;
        }

        return false;
    }

    /**
     * Formats the FCM message for us
     *
     * @param Message $message
     * @return FcmMessage
     */
    public function formatPushNotification(Message $message): FcmMessage
    {
        $pushNotification = new FcmMessage();

        $pushNotification->priority(FcmMessage::PRIORITY_HIGH);
        $pushNotification->contentAvailable(true);

        $notificationData = $message->data;

        if ($message->action) {
            $pushNotification->data([
                'action' => $message->action,
                'click_action' => $message->action,
            ]);
        }

        $pushNotification->content($notificationData);

        return $pushNotification;
    }

    /**
     * Sends a formatted push notification to the passed in key
     *
     * @param FcmMessage $pushNotification
     * @param PushNotificationKey $pushNotificationKey
     * @return void
     */
    public function sendPushNotification(FcmMessage $pushNotification, PushNotificationKey $pushNotificationKey): bool
    {
        $pushNotification->to($pushNotificationKey->push_notification_key);
        try {
            $this->client->post(FcmChannel::API_URI, [
                'headers' => [
                    'Authorization' => 'key=' . $this->fcmKey,
                    'Content-Type' => 'application/json',
                ],
                'body' => $pushNotification->formatData(),
            ]);
            return true;
        } catch (GuzzleException $e) {
            $error = 'Failed Sending Push Notification - ' . $e->getMessage();
            $this->logger->error($error, $e->getTrace());

            return false;
        }
    }
}