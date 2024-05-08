<?php
declare(strict_types=1);

namespace App\Contracts\Services\Collection;

use App\Contracts\Models\CanBeMorphedToContract;
use App\Contracts\Models\IsAnEntityContract;

interface ItemInEntityCollectionServiceContract
{
    /**
     * Tells us whether the passed in item is in any collections a entity has
     *
     * @param IsAnEntityContract $entity
     * @param CanBeMorphedToContract $item
     * @return bool
     */
    public function isItemInEntityCollection(IsAnEntityContract $entity, CanBeMorphedToContract $item): bool;
}