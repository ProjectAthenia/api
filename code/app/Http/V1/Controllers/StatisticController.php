<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers;

use App\Athenia\Http\Core\Controllers\StatisticControllerAbstract;
use App\Athenia\Contracts\Repositories\Statistics\StatisticRepositoryContract;

/**
 * Class StatisticController
 * @package App\Http\V1\Controllers
 */
class StatisticController extends StatisticControllerAbstract
{
    /**
     * StatisticController constructor.
     * @param StatisticRepositoryContract $repository
     */
    public function __construct(StatisticRepositoryContract $repository)
    {
        $this->repository = $repository;
    }
} 