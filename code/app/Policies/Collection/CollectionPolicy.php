<?php
declare(strict_types=1);

namespace App\Policies\Collection;

use App\Contracts\Models\IsAnEntity;
use App\Models\Collection\Collection;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

class CollectionPolicy extends BasePolicyAbstract
{
    /**
     * Indexes all organization managers
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return true;
    }

    /**
     * Only organization admins can create new organization managers
     *
     * @param User $user
     * @param IsAnEntity $entity
     * @return bool
     */
    public function create(User $user, IsAnEntity $entity)
    {
        return $entity->canUserManageEntity($user, Role::MANAGER);
    }

    /**
     * Only organization admins can update organization managers
     *
     * @param User $user
     * @param Collection $collection
     * @return bool
     */
    public function view(User $user, Collection $collection)
    {
        /** @var IsAnEntity $entity */
        $entity = $collection->owner;
        return $collection->is_public || $entity->canUserManageEntity($user, Role::MANAGER);
    }

    /**
     * Only organization admins can update organization managers
     *
     * @param User $user
     * @param Collection $collection
     * @return bool
     */
    public function update(User $user, Collection $collection)
    {
        /** @var IsAnEntity $entity */
        $entity = $collection->owner;
        return $entity->canUserManageEntity($user, Role::MANAGER);
    }

    /**
     * Only organization admins can delete organization managers
     *
     * @param User $user
     * @param Collection $collection
     * @return bool
     */
    public function delete(User $user, Collection $collection)
    {
        /** @var IsAnEntity $entity */
        $entity = $collection->owner;
        return $entity->canUserManageEntity($user, Role::MANAGER);
    }
}