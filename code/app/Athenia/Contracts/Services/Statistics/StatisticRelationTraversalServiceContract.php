<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Models\Statistics\Statistic;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface StatisticRelationTraversalServiceContract
 * @package App\Athenia\Contracts\Services\Statistics
 */
interface StatisticRelationTraversalServiceContract
{
    /**
     * Takes a statistic and a target, and traverses through the relation chain specified
     * in the statistic to return all models at the end of the relation chain
     *
     * @param Statistic $statistic The statistic containing the relation path
     * @param CanBeStatisticTargetContract $target The model to start traversing from
     * @return Collection The collection of models at the end of the relation chain
     */
    public function getRelatedModels(Statistic $statistic, CanBeStatisticTargetContract $target): Collection;
} 