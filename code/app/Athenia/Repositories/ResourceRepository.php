<?php
declare(strict_types=1);

namespace App\Athenia\Repositories;

use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Models\Resource;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ResourceRepository
 * @package App\Repositories
 */
class ResourceRepository extends BaseRepositoryAbstract implements ResourceRepositoryContract
{
    /**
     * ResourceRepository constructor.
     * @param Resource $model
     * @param LogContract $log
     */
    public function __construct(Resource $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}