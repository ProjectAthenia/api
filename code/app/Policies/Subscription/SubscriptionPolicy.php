<?php
declare(strict_types=1);

namespace App\Policies\Subscription;

use App\Contracts\Models\IsAnEntityContract;
use App\Models\Role;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class SubscriptionPolicy
 * @package App\Policies\Subscription
 */
class SubscriptionPolicy extends BasePolicyAbstract
{
    /**
     * Only available for super admins and admins of the entity
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function all(User $loggedInUser, IsAnEntityContract $entity)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR);
    }

    /**
     * Only Available for super admins
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function create(User $loggedInUser, IsAnEntityContract $entity)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR);
    }

    /**
     * Only available to super admins
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @param Subscription $subscription
     * @return bool
     */
    public function update(User $loggedInUser, IsAnEntityContract $entity, Subscription $subscription)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR)
            && $subscription->subscriber_type == $entity->morphRelationName()
            && $subscription->subscriber_id == $entity->id;
    }
}