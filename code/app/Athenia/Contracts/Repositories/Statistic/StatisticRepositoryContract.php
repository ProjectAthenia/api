<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Repositories\Statistic;

use App\Athenia\Contracts\Repositories\BaseRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Interface StatisticRepositoryContract
 */
interface StatisticRepositoryContract extends BaseRepositoryContract
{
    /**
     * Get all statistics for a given model
     *
     * @param string $model
     * @return Collection
     */
    public function findAllForModel(string $model): Collection;
} 