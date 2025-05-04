<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Models\Statistics\TargetStatistic;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class TargetStatisticRepository
 * @package App\Athenia\Repositories\Statistics
 */
class TargetStatisticRepository extends BaseRepositoryAbstract implements TargetStatisticRepositoryContract
{
    /**
     * TargetStatisticRepository constructor.
     * @param TargetStatistic $model
     * @param LogContract $log
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        TargetStatistic $model,
        LogContract $log,
        private readonly Dispatcher $dispatcher
    ) {
        parent::__construct($model, $log);
    }

    /**
     * Creates a new target statistic model
     *
     * @param CanBeStatisticTargetContract $target The target model to create statistics for
     * @param array<string, mixed> $data The data to create the statistic with
     * @return TargetStatistic The newly created target statistic
     */
    public function createForTarget(CanBeStatisticTargetContract $target, array $data): TargetStatistic
    {
        $data['target_id'] = $target->id;
        $data['target_type'] = $target->morphRelationName();

        return $this->create($data);
    }

    /**
     * Find all statistics for a specific target
     *
     * @param CanBeStatisticTargetContract $target The target model to find statistics for
     * @return Collection<int, TargetStatistic> Collection of target statistics
     */
    public function findAllForTarget(CanBeStatisticTargetContract $target): Collection
    {
        return $this->model
            ->where('target_type', $target->morphRelationName())
            ->where('target_id', $target->id)
            ->get();
    }

    /**
     * Find a specific statistic for a target
     *
     * @param CanBeStatisticTargetContract $target The target model to find the statistic for
     * @param int $statisticId The ID of the statistic to find
     * @return TargetStatistic|null The found target statistic or null if not found
     */
    public function findForTarget(CanBeStatisticTargetContract $target, int $statisticId): ?TargetStatistic
    {
        return $this->model
            ->where('target_type', $target->morphRelationName())
            ->where('target_id', $target->id)
            ->where('statistic_id', $statisticId)
            ->first();
    }
} 