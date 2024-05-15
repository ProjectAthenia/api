<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Models\Subscription\Subscription;

/**
 * Interface EntitySubscriptionCreationService
 * @package App\Contracts\Services
 */
interface EntitySubscriptionCreationServiceContract
{
    /**
     * Creates a subscription for an entity while replacing any current one that may exist for an entity
     *
     * @param IsAnEntityContract $entity
     * @param array $data
     * @return Subscription
     */
    public function createSubscription(IsAnEntityContract $entity, array $data): Subscription;
}
