<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models;

/**
 * Interface CanBeAggregatedContract
 * @package App\Athenia\Contracts\Models
 */
interface CanBeAggregatedContract
{
    /**
     * Returns the relation path to the models that can be target statistics
     * For example: "collectionItem.collection" would mean this model affects statistics on collections
     * through the collectionItem relation
     *
     * @return string
     */
    public function getStatisticTargetRelationPath(): string;
} 