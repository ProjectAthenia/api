<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;

/**
 * Class UserMessagesMergeListener
 * @package App\Listeners\User\UserMerge
 */
class UserMessagesMergeListener
{
    /**
     * @var MessageRepositoryContract
     */
    private $messageRepository;

    /**
     * UserMessagesMergeListener constructor.
     * @param MessageRepositoryContract $messageRepository
     */
    public function __construct(MessageRepositoryContract $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        if ($mergeOptions['messages'] ?? false) {
            foreach ($mergeUser->messages as $message) {
                $this->messageRepository->update($message, [
                    'user_id' => $mainUser->id,
                ]);
            }
        }
    }
}