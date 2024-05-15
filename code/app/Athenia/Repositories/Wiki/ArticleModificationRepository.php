<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleModificationRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Wiki\ArticleModification;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleModificationRepository
 * @package App\Repositories\Wiki
 */
class ArticleModificationRepository extends BaseRepositoryAbstract implements ArticleModificationRepositoryContract
{
    /**
     * ArticleModificationRepository constructor.
     * @param ArticleModification $model
     * @param LogContract $log
     */
    public function __construct(ArticleModification $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}
