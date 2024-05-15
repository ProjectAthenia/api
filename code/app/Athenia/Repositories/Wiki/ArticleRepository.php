<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Wiki\Article;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleRepository
 * @package App\Repositories\Wiki
 */
class ArticleRepository extends BaseRepositoryAbstract implements ArticleRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Delete;

    /**
     * ArticleRepository constructor.
     * @param Article $model
     * @param LogContract $log
     */
    public function __construct(Article $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}