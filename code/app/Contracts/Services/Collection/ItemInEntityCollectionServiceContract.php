<?php
declare(strict_types=1);

namespace App\Contracts\Services\Collection;

use App\Contracts\Models\CanBeMorphedTo;
use App\Contracts\Models\IsAnEntity;

interface ItemInEntityCollectionServiceContract
{
    /**
     * Tells us whether the passed in item is in any collections a entity has
     *
     * @param IsAnEntity $entity
     * @param CanBeMorphedTo $item
     * @return bool
     */
    public function isItemInEntityCollection(IsAnEntity $entity, CanBeMorphedTo $item): bool;
}