<?php
declare(strict_types=1);

namespace App\Policies\Collection;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

class CollectionItemPolicy extends BasePolicyAbstract
{
    /**
     * Everyone can see public collection items, but non public ones are restriced
     *
     * @param User $user
     * @param Collection $collection
     * @return bool
     */
    public function all(User $user, Collection $collection)
    {
        return $collection->is_public || $collection->owner->canUserManageEntity($user, Role::MANAGER);
    }

    /**
     * Only people related to the collection owner can see it
     *
     * @param User $user
     * @param Collection $collection
     * @return bool
     */
    public function create(User $user, Collection $collection)
    {
        return $collection->owner->canUserManageEntity($user, Role::MANAGER);
    }

    /**
     * Anyone can view public collection items
     *
     * @param User $user
     * @param Collection $collection
     * @param CollectionItem $collectionItem
     * @return bool
     */
    public function view(User $user, Collection $collection, CollectionItem $collectionItem)
    {
        return $collection->id == $collectionItem->collection_id &&
            ($collection->is_public || $collection->owner->canUserManageEntity($user, Role::MANAGER));
    }

    /**
     * Only people related to the owner can update a collection item
     *
     * @param User $user
     * @param Collection $collection
     * @param CollectionItem $collectionItem
     * @return bool
     */
    public function update(User $user, Collection $collection, CollectionItem $collectionItem)
    {
        return $collection->id == $collectionItem->collection_id &&
            $collection->owner->canUserManageEntity($user, Role::MANAGER);
    }

    /**
     * Only people related to the owner can delete a collection item
     *
     * @param User $user
     * @param Collection $collection
     * @param CollectionItem $collectionItem
     * @return bool
     */
    public function delete(User $user, Collection $collection, CollectionItem $collectionItem)
    {
        return $collection->id == $collectionItem->collection_id &&
            $collection->owner->canUserManageEntity($user, Role::MANAGER);
    }
}