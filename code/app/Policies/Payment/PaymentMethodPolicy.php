<?php
declare(strict_types=1);

namespace App\Policies\Payment;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Payment\PaymentMethod;
use App\Models\Role;
use App\Models\User\User;

/**
 * Class PaymentMethodPolicy
 * @package App\Policies\Payment
 */
class PaymentMethodPolicy extends BasePolicyAbstract
{
    /**
     * Any logged in users can create a payment methods
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
     * Any logged in entity can update their own payment method
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function update(User $loggedInUser, IsAnEntityContract $entity, PaymentMethod $paymentMethod)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR)
            && $paymentMethod->owner_type == $entity->morphRelationName()
            && $paymentMethod->owner_id == $entity->id;
    }

    /**
     * Any logged in users can delete their own payment method
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function delete(User $loggedInUser, IsAnEntityContract $entity, PaymentMethod $paymentMethod)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR)
            && $paymentMethod->owner_type == $entity->morphRelationName()
            && $paymentMethod->owner_id == $entity->id;
    }
}
