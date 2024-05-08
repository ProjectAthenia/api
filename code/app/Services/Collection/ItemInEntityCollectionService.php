<?php
declare(strict_types=1);

namespace App\Services\Collection;

use App\Contracts\Models\CanBeMorphedToContract;
use App\Contracts\Models\IsAnEntityContract;
use App\Contracts\Services\Collection\ItemInEntityCollectionServiceContract;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\User\User;

class ItemInEntityCollectionService implements ItemInEntityCollectionServiceContract
{
    /**
     * Tells us whether the passed in item is in any collections a entity has
     *
     * @param User $entity
     * @param CanBeMorphedToContract $item
     * @return bool
     */
    public function isItemInEntityCollection(IsAnEntityContract $entity, CanBeMorphedToContract $item): bool
    {
        $collectionItems = $entity->collections->flatMap(fn (Collection $i) => $i->collectionItems);

        $maybeCollectionItem = $collectionItems
            ->first(fn (CollectionItem $i) => $i->item_type == $item->morphRelationName() && $i->item_id == $item->id);

        return !!$maybeCollectionItem;
    }
}