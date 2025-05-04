<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Statistics;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Model;
use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;

/**
 * Interface TargetStatisticRepositoryContract
 * @package App\Athenia\Contracts\Repositories\Statistics
 */
interface TargetStatisticRepositoryContract extends BaseRepositoryContract
{
    /**
     * Creates a new target statistic model
     *
     * @param CanBeStatisticTargetContract $target
     * @param array $data
     * @return TargetStatistic
     */
    public function createForTarget(CanBeStatisticTargetContract $target, array $data): TargetStatistic;

    /**
     * Find all statistics for a specific target
     *
     * @param CanBeStatisticTargetContract $target
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllForTarget(CanBeStatisticTargetContract $target);

    /**
     * Find a specific statistic for a target
     *
     * @param CanBeStatisticTargetContract $target
     * @param int $statisticId
     * @return TargetStatistic|null
     */
    public function findForTarget(CanBeStatisticTargetContract $target, int $statisticId): ?TargetStatistic;
} 