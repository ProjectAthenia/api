<?php
declare(strict_types=1);

namespace App\Contracts\Services;

use App\Contracts\Models\IsAnEntityContract;

/**
 * Interface EntityFeatureAccessServiceContract
 * @package App\Contracts\Services
 */
interface EntityFeatureAccessServiceContract
{
    /**
     * Tells us whether or not the passed in entity can acess the related feature ID
     *
     * @param IsAnEntityContract $entity
     * @param int $featureId
     * @return bool
     */
    public function canAccess(IsAnEntityContract $entity, int $featureId): bool;
}
