<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Statistic;

use App\Models\Statistic\Statistic;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Contracts\Repositories\Statistic\StatisticRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Events\Statistic\StatisticUpdatedEvent;
use App\Athenia\Events\Statistic\StatisticCreatedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Log\LoggerInterface as LogContract;
use App\Athenia\Repositories\Statistic\StatisticFilterRepository;
use App\Athenia\Traits\CanGetAndUnset;
use App\Athenia\Events\Statistic\StatisticDeletedEvent;

/**
 * Class StatisticRepository
 */
class StatisticRepository extends BaseRepositoryAbstract implements StatisticRepositoryContract
{
    use CanGetAndUnset;

    public function __construct(
        Statistic $model,
        LogContract $log,
        private readonly StatisticFilterRepository $statisticFilterRepository,
        private readonly Dispatcher $dispatcher
    ) {
        parent::__construct($model, $log);
    }

    /**
     * @inheritDoc
     */
    public function model(): string
    {
        return Statistic::class;
    }

    /**
     * @inheritDoc
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $statisticFilters = $this->getAndUnset($data, 'statistic_filters');

        $model = parent::update($model, $data, $forcedValues);

        if ($statisticFilters !== null) {
            $this->syncChildModels(
                $this->statisticFilterRepository,
                $model,
                $statisticFilters,
                $model->statisticFilters
            );
        }

        $this->dispatcher->dispatch(new StatisticUpdatedEvent($model));

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data = [], ?BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $statisticFilters = $this->getAndUnset($data, 'statistic_filters') ?? [];

        $model = parent::create($data, $relatedModel, $forcedValues);

        if ($statisticFilters) {
            $this->syncChildModels(
                $this->statisticFilterRepository,
                $model,
                $statisticFilters
            );
        }

        $this->dispatcher->dispatch(new StatisticCreatedEvent($model));

        return $model;
    }

    public function delete(BaseModelAbstract $model): void
    {
        parent::delete($model);
        $this->dispatcher->dispatch(new StatisticDeletedEvent($model));
    }
} 