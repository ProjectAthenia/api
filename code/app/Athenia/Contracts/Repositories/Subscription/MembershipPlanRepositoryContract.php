<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Subscription;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\Subscription\MembershipPlan;

/**
 * Interface MembershipPlanRepositoryContract
 * @package App\Contracts\Repositories\Subscription
 */
interface MembershipPlanRepositoryContract extends BaseRepositoryContract
{
    /**
     * Finds the default membership plan that will be applied to an entity if the entity is not subscribed
     *
     * @param string $entityType
     * @return MembershipPlan|null
     */
    public function findDefaultMembershipPlanForEntity(string $entityType): ?MembershipPlan;
}
