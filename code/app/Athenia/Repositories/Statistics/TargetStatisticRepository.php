<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\TargetStatisticRepositoryContract;
use App\Models\Statistics\TargetStatistic;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TargetStatisticRepository
 * @package App\Athenia\Repositories\Statistics
 */
class TargetStatisticRepository extends BaseRepositoryAbstract implements TargetStatisticRepositoryContract
{
    /**
     * @var TargetStatistic
     */
    protected $model;

    /**
     * TargetStatisticRepository constructor.
     * @param TargetStatistic $model
     * @param Dispatcher $dispatcher
     */
    public function __construct(TargetStatistic $model, Dispatcher $dispatcher)
    {
        parent::__construct($model, $dispatcher);
    }

    /**
     * Creates a new target statistic model
     *
     * @param Model $target
     * @param array $data
     * @return TargetStatistic
     */
    public function createForTarget(Model $target, array $data): TargetStatistic
    {
        $data['target_id'] = $target->id;
        $data['target_type'] = get_class($target);

        return $this->create($data);
    }

    /**
     * Find all statistics for a specific target
     *
     * @param Model $target
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllForTarget(Model $target)
    {
        return $this->model
            ->where('target_type', get_class($target))
            ->where('target_id', $target->id)
            ->get();
    }

    /**
     * Find a specific statistic for a target
     *
     * @param Model $target
     * @param int $statisticId
     * @return TargetStatistic|null
     */
    public function findForTarget(Model $target, int $statisticId): ?TargetStatistic
    {
        return $this->model
            ->where('target_type', get_class($target))
            ->where('target_id', $target->id)
            ->where('statistic_id', $statisticId)
            ->first();
    }
} 