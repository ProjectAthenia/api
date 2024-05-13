<?php
declare(strict_types=1);

namespace App\Listeners\Messaging;

use App\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Events\Messaging\MessageCreatedEvent;
use App\Events\Messaging\MessageSentEvent;
use App\Mail\MessageMailer;
use App\Models\Messaging\Message;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
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
                                private Repository $config)
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
        }
    }
}
