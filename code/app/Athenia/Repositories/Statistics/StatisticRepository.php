<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Statistics;

use App\Models\Statistics\Statistic;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Events\Statistics\StatisticCreatedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Log\LoggerInterface as LogContract;
use App\Athenia\Repositories\Statistics\StatisticFilterRepository;

/**
 * Class StatisticRepository
 */
class StatisticRepository extends BaseRepositoryAbstract implements StatisticRepositoryContract
{
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
        $statisticFilters = $data['statistic_filters'] ?? [];
        unset($data['statistic_filters']);

        $model = parent::update($model, $data, $forcedValues);

        if ($statisticFilters) {
            // Delete all existing filters
            foreach ($model->statisticFilters as $filter) {
                $this->statisticFilterRepository->delete($filter);
            }

            // Create new filters
            foreach ($statisticFilters as $filter) {
                $this->statisticFilterRepository->create($filter, $model);
            }

            // Refresh the relationship
            $model->load('statisticFilters');
        }

        $this->dispatcher->dispatch(new StatisticUpdatedEvent($model));

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data = [], ?BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $statisticFilters = $data['statistic_filters'] ?? [];
        unset($data['statistic_filters']);

        $model = parent::create($data, $relatedModel, $forcedValues);

        if ($statisticFilters) {
            foreach ($statisticFilters as $filter) {
                $this->statisticFilterRepository->create($filter, $model);
            }
        }

        $this->dispatcher->dispatch(new StatisticCreatedEvent($model));

        return $model;
    }
} 