<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;

/**
 * Class UserSubscriptionsMergeListener
 * @package App\Listeners\User\UserMerge
 */
class UserSubscriptionsMergeListener
{
    /**
     * @var SubscriptionRepositoryContract
     */
    private $subscriptionRepository;

    /**
     * UserSubscriptionsMergeListener constructor.
     * @param SubscriptionRepositoryContract $subscriptionRepository
     */
    public function __construct(SubscriptionRepositoryContract $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * checks to merge subscriptions from a user
     *
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        if ($mergeOptions['subscriptions'] ?? false) {
            foreach ($mergeUser->subscriptions as $subscription) {
                $this->subscriptionRepository->update($subscription, [
                    'owner_id' => $mainUser->id,
                    // the old users payment methods will not work with the new user data
                    'payment_method_id' => null,
                ]);
            }
        }
    }
}