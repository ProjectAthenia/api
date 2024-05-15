<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Wiki\ArticleIteration;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleIterationRepository
 * @package App\Repositories\Wiki
 */
class ArticleIterationRepository extends BaseRepositoryAbstract implements ArticleIterationRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Delete, \App\Athenia\Repositories\Traits\NotImplemented\Update;

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
