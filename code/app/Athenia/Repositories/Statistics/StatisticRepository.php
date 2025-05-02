<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Statistics;

use App\Models\Statistics\Statistic;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;

/**
 * Class StatisticRepository
 */
class StatisticRepository extends BaseRepositoryAbstract implements StatisticRepositoryContract
{
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
        $model = parent::update($model, $data, $forcedValues);

        if (isset($data['statistic_filters'])) {
            $model->statisticFilters()->delete();
            foreach ($data['statistic_filters'] as $filter) {
                $model->statisticFilters()->create($filter);
            }
        }

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
                $model->statisticFilters()->create($filter);
            }
        }

        return $model;
    }
} 