<?php

declare(strict_types=1);

namespace App\Athenia\Repositories\Statistic;

use App\Athenia\Contracts\Repositories\Statistic\StatisticFilterRepositoryContract;
use App\Models\Statistic\StatisticFilter;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

class StatisticFilterRepository extends BaseRepositoryAbstract implements StatisticFilterRepositoryContract
{
    public function __construct(StatisticFilter $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
} 