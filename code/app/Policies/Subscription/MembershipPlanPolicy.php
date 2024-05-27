<?php
declare(strict_types=1);

namespace App\Policies\Subscription;

use App\Athenia\Contracts\Policies\BasePolicyContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Subscription\MembershipPlan;
use App\Models\User\User;

/**
 * Class MembershipPlanPolicy
 * @package App\Policies\Subscription
 */
class MembershipPlanPolicy extends BasePolicyAbstract implements BasePolicyContract
{
    /**
     * All users can index membership plans
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return true;
    }

    /**
     * All users can see a membership plan
     *
     * @param User $user
     * @param MembershipPlan $membershipPlan
     * @return bool
     */
    public function view(User $user, MembershipPlan $membershipPlan)
    {
        return true;
    }

    /**
     * Only Available for super admins
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Only available to super admins
     *
     * @param User $user
     * @param MembershipPlan $membershipPlan
     * @return bool
     */
    public function update(User $user, MembershipPlan $membershipPlan)
    {
        return false;
    }

    /**
     * Only available to super admins
     *
     * @param User $user
     * @param MembershipPlan $membershipPlan
     * @return bool
     */
    public function delete(User $user, MembershipPlan $membershipPlan)
    {
        return false;
    }
}