<?php
declare(strict_types=1);

namespace App\Policies;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Asset;
use App\Models\User\User;

/**
 * Class AssetPolicy
 * @package App\Policies
 */
class AssetPolicy extends BasePolicyAbstract
{
    /**
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function all(User $loggedInUser, IsAnEntityContract $entity)
    {
        return $entity->canUserManageEntity($loggedInUser);
    }

    /**
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function create(User $loggedInUser, IsAnEntityContract $entity)
    {
        return $entity->canUserManageEntity($loggedInUser);
    }

    /**
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @param Asset $asset
     * @return bool
     */
    public function update(User $loggedInUser, IsAnEntityContract $entity, Asset $asset)
    {
        return $asset->owner_type == $entity->morphRelationName() && $asset->owner_id == $entity->id
            && $entity->canUserManageEntity($loggedInUser);
    }

    /**
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @param Asset $asset
     * @return bool
     */
    public function delete(User $loggedInUser, IsAnEntityContract $entity, Asset $asset)
    {
        return $asset->owner_type == $entity->morphRelationName() && $asset->owner_id == $entity->id
            && $entity->canUserManageEntity($loggedInUser);
    }
}