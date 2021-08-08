<?php
declare(strict_types=1);

namespace App\Repositories\Wiki;

use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Models\Wiki\ArticleIteration;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class IterationRepository
 * @package App\Repositories\Wiki
 */
class IterationRepository extends BaseRepositoryAbstract implements IterationRepositoryContract
{
    use NotImplemented\Delete, NotImplemented\Update;

    /**
     * IterationRepository constructor.
     * @param ArticleIteration $model
     * @param LogContract $log
     */
    public function __construct(ArticleIteration $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}
