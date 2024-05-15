<?php
declare(strict_types=1);

namespace App\Policies\Subscription;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;

/**
 * Class MembershipPlanRatePolicy
 * @package App\Policies\Subscription
 */
class MembershipPlanRatePolicy extends BasePolicyAbstract
{
    /**
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return false;
    }
}