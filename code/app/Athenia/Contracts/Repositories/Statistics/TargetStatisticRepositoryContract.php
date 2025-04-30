<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Statistics;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use App\Models\Statistics\TargetStatistic;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface TargetStatisticRepositoryContract
 * @package App\Athenia\Contracts\Repositories\Statistics
 */
interface TargetStatisticRepositoryContract extends BaseRepositoryContract
{
    /**
     * Creates a new target statistic model
     *
     * @param Model $target
     * @param array $data
     * @return TargetStatistic
     */
    public function createForTarget(Model $target, array $data): TargetStatistic;

    /**
     * Find all statistics for a specific target
     *
     * @param Model $target
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllForTarget(Model $target);

    /**
     * Find a specific statistic for a target
     *
     * @param Model $target
     * @param int $statisticId
     * @return TargetStatistic|null
     */
    public function findForTarget(Model $target, int $statisticId): ?TargetStatistic;
} 