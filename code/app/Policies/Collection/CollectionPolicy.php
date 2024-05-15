<?php
declare(strict_types=1);

namespace App\Policies\Collection;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Collection\Collection;
use App\Models\Role;
use App\Models\User\User;

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
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function create(User $user, IsAnEntityContract $entity)
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
        /** @var IsAnEntityContract $entity */
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
        /** @var IsAnEntityContract $entity */
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
        /** @var IsAnEntityContract $entity */
        $entity = $collection->owner;
        return $entity->canUserManageEntity($user, Role::MANAGER);
    }
}