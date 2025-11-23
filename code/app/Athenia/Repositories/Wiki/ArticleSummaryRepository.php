<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleSummaryRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Wiki\ArticleSummary;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleSummaryRepository
 * @package App\Athenia\Repositories\Wiki
 */
class ArticleSummaryRepository extends BaseRepositoryAbstract implements ArticleSummaryRepositoryContract
{
    /**
     * ArticleSummaryRepository constructor.
     * @param ArticleSummary $model
     * @param LogContract $log
     */
    public function __construct(ArticleSummary $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}
