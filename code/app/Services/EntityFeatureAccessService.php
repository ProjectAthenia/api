<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\Models\IsAnEntityContract;
use App\Contracts\Repositories\Subscription\MembershipPlanRepositoryContract;
use App\Contracts\Services\EntityFeatureAccessServiceContract;

/**
 * Class EntityFeatureAccessService
 * @package App\Services
 */
class EntityFeatureAccessService implements EntityFeatureAccessServiceContract
{
    /**
     * @var MembershipPlanRepositoryContract
     */
    private MembershipPlanRepositoryContract $membershipPlanRepository;

    /**
     * EntityFeatureAccessService constructor.
     * @param MembershipPlanRepositoryContract $membershipPlanRepository
     */
    public function __construct(MembershipPlanRepositoryContract $membershipPlanRepository)
    {
        $this->membershipPlanRepository = $membershipPlanRepository;
    }

    /**
     * Tells us whether or not the passed in entity can acess the related feature ID
     *
     * @param IsAnEntityContract $entity
     * @param int $featureId
     * @return bool
     */
    public function canAccess(IsAnEntityContract $entity, int $featureId): bool
    {
        $subscription = $entity->currentSubscription();

        if ($subscription) {
            $membershipPlan = $subscription->membershipPlanRate->membershipPlan;
        } else {
            $membershipPlan = $this->membershipPlanRepository->findDefaultMembershipPlanForEntity(
                $entity->morphRelationName()
            );
        }
        return $membershipPlan ? $membershipPlan->features->pluck('id')->contains($featureId) : false;
    }
}
