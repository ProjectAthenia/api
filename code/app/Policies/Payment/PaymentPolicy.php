<?php
declare(strict_types=1);

namespace App\Policies\Payment;

use App\Contracts\Models\IsAnEntityContract;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class PaymentMethodPolicy
 * @package App\Policies\Payment
 */
class PaymentPolicy extends BasePolicyAbstract
{
    /**
     * Only available for super admins and people related to the entity
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function all(User $loggedInUser, IsAnEntityContract $entity)
    {
        return $entity->canUserManageEntity($loggedInUser);
    }
}