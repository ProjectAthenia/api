<?php
declare(strict_types=1);

namespace App\Athenia\Services\Messaging;

use App\Athenia\Contracts\Models\Messaging\CanReceiveMessageContract;
use App\Athenia\Contracts\Models\Messaging\CanReceiveSlackNotificationsContract;
use App\Athenia\Contracts\Services\Messaging\SendSlackNotificationServiceContract;
use App\Models\Messaging\Message;
use JoliCode\Slack\ClientFactory;

class SendSlackNotificationService implements SendSlackNotificationServiceContract
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
        if ($receiver instanceof CanReceiveSlackNotificationsContract && $receiver->canReceiveMessage($message)) {

            $slackClient = ClientFactory::create($receiver->getSlackKey($message));

            $data = [
                'username' => 'Pomelo Productions Monitoring Bot',
                'channel' => $receiver->getSlackChannel($message),
                'text' => $message->subject,
            ];
            if (isset ($message->data['slack_text'])) {
                $data['blocks'] = json_encode([
                    [
                        'type' => 'text',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => $message->data['slack_text'],
                        ],
                    ],
                ]);
            }

            $slackClient->chatPostMessage($data);

            return true;
        }

        return false;
    }
}