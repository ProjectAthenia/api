<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Statistics;

use App\Athenia\Models\Statistics\Statistic;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;

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
    public function update($model, array $data)
    {
        $model = parent::update($model, $data);

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
    public function create(array $data)
    {
        $model = parent::create($data);

        if (isset($data['statistic_filters'])) {
            foreach ($data['statistic_filters'] as $filter) {
                $model->statisticFilters()->create($filter);
            }
        }

        return $model;
    }
} 