<?php
declare(strict_types=1);

namespace App\Listeners\Message;

use App\Contracts\Models\CanReceiveTextMessagesContract;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Events\Message\MessageCreatedEvent;
use App\Events\Message\MessageSentEvent;
use App\Mail\MessageMailer;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\User\Message;
use App\Models\User\User;
use Benwilkins\FCM\FcmChannel;
use Benwilkins\FCM\FcmMessage;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\Twilio;
use NotificationChannels\Twilio\TwilioSmsMessage;

/**
 * Class MessageCreatedListener
 * @package App\Listeners\Message
 */
class MessageCreatedListener implements ShouldQueue
{
    /**
     * MessageCreatedListener constructor.
     * @param Mailer $mailer
     * @param Client $client
     * @param MessageRepositoryContract $messageRepository
     * @param Dispatcher $events
     * @param Repository $config
     * TODO revamp for
     */
    public function __construct(private Mailer $mailer,
                                private Client $client,
                                private MessageRepositoryContract $messageRepository,
                                private Dispatcher $events,
                                private Repository $config,
                                private Twilio $twilio)
    {}

    /**
     * Schedules the message to be sent
     *
     * @param MessageCreatedEvent $event
     * @throws Exception
     * @todo revamp so that each delivery means has it's own contract that can be detected
     *      and so that an additional contract can provide entities that should receive the message
     */
    public function handle(MessageCreatedEvent $event)
    {
        $message = $event->getMessage();

        $this->messageRepository->update($message, [
            'scheduled_at' => Carbon::now(),
        ]);

        $via = $message->via ?? [Message::VIA_EMAIL];

        if (in_array(Message::VIA_PUSH_NOTIFICATION, $via)) {

            try {
                $message->fresh();

                /** @var User|Organization $to */
                $to = $message->to;

                if ($to) {
                    if ($to->morphRelationName() == 'organization') {
                        $to->organizationManagers
                            ->filter(fn (OrganizationManager $i) => $i->user)
                            ->each(fn (OrganizationManager $i) => $this->sendPushNotification($i->user, $message));
                    } else {
                        $this->sendPushNotification($to, $message);
                    }
                }

                $this->messageRepository->update($message, [
                    'sent_at' => Carbon::now(),
                ]);

            } catch (Exception $exception) {}
            $this->events->dispatch(new MessageSentEvent($message));
        }
        if (in_array(Message::VIA_EMAIL, $via) && ($message->email || ($message->to && $message->to->email))) {
            $this->mailer->send(new MessageMailer($message));
        }
        if (in_array(Message::VIA_SMS, $via)) {
            $sms = new TwilioSmsMessage($message->data['message']);
            /** @var Organization|User $to */
            $to = $message->to;
            if ($to instanceof CanReceiveTextMessagesContract) {
                $this->twilio->sendMessage($sms, $to->routeNotificationForTwilio());
                $this->events->dispatch(new MessageSentEvent($message));
            } else if ($to instanceof Organization) {
                foreach ($to->organizationManagers as $organizationManager) {
                    $user = $organizationManager->user;
                    if ($user instanceof CanReceiveTextMessagesContract) {
                        $this->twilio->sendMessage($sms, $user->routeNotificationForTwilio());
                        $this->events->dispatch(new MessageSentEvent($message));
                    }
                }
            }
        }
    }

    public function sendMessage(User $user, Message $message)
    {

    }

    /**
     * @param User $user
     * @param Message $message
     * @throws Exception
     */
    public function sendPushNotification(User $user, Message $message)
    {
        if ($user->pushNotificationKeys->count() && $user->receive_push_notifications) {
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

            $fcmKey = $this->config->get('services.fcm.key');

            foreach ($user->pushNotificationKeys as $pushNotificationKey){
                $pushNotification->to($pushNotificationKey->push_notification_key);
                $this->client->post(FcmChannel::API_URI, [
                    'headers' => [
                        'Authorization' => 'key=' . $fcmKey,
                        'Content-Type' => 'application/json',
                    ],
                    'body' => $pushNotification->formatData(),
                ]);
            }
        }
    }
}
