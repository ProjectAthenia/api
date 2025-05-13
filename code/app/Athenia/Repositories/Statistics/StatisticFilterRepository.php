<?php

declare(strict_types=1);

namespace App\Athenia\Repositories\Statistics;

use App\Athenia\Contracts\Repositories\Statistics\StatisticFilterRepositoryContract;
use App\Models\Statistics\StatisticFilter;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

class StatisticFilterRepository extends BaseRepositoryAbstract implements StatisticFilterRepositoryContract
{
    public function __construct(StatisticFilter $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
} 