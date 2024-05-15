<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleVersionRepositoryContract;
use App\Athenia\Events\Article\ArticleVersionCreatedEvent;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleVersionRepository
 * @package App\Repositories\Wiki
 */
class ArticleVersionRepository extends BaseRepositoryAbstract implements ArticleVersionRepositoryContract
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * ArticleVersionRepository constructor.
     * @param ArticleVersion $model
     * @param LogContract $log
     */
    public function __construct(ArticleVersion $model, LogContract $log, Dispatcher $dispatcher)
    {
        parent::__construct($model, $log);
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array $data
     * @param Article|BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $oldVersion = $relatedModel->current_version;
        /** @var ArticleVersion $newVersion */
        $newVersion = parent::create($data, $relatedModel, $forcedValues);

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);

        $this->dispatcher->dispatch($event);

        return $newVersion;
    }
}