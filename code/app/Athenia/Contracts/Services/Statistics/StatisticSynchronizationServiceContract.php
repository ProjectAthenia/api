<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Statistics;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Statistics\Statistic;

/**
 * Interface StatisticSynchronizationServiceContract
 * @package App\Athenia\Contracts\Services\Statistics
 */
interface StatisticSynchronizationServiceContract
{
    /**
     * Takes in a model that can be a statistic target, and ensures that all necessary target
     * statistics exist for that model based on the available statistics for its type
     *
     * @param CanBeStatisticTargetContract $model
     * @return Collection|TargetStatistic[]
     */
    public function synchronizeTargetStatistics(CanBeStatisticTargetContract $model): Collection;

    /**
     * Create target statistics for a newly created statistic.
     *
     * @param Statistic $statistic
     * @return Collection|TargetStatistic[]
     */
    public function createTargetStatisticsForStatistic(Statistic $statistic): Collection;
} 