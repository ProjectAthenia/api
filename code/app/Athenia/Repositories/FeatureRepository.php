<?php
declare(strict_types=1);

namespace App\Athenia\Repositories;

use App\Athenia\Contracts\Repositories\FeatureRepositoryContract;
use App\Models\Feature;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class FeatureRepository
 * @package App\Repositories
 */
class FeatureRepository extends BaseRepositoryAbstract implements FeatureRepositoryContract
{
    /**
     * FeatureRepository constructor.
     * @param Feature $model
     * @param LogContract $log
     */
    public function __construct(Feature $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}
