<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Wiki;

use App\Athenia\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
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
    use CanGetAndUnset;

    /**
     * ArticleRepository constructor.
     * @param Article $model
     * @param LogContract $log
     */
    public function __construct(Article $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * Override create to handle categories sync
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = []): BaseModelAbstract
    {
        $categories = $this->getAndUnset($data, 'categories');

        /** @var Article $article */
        $article = parent::create($data, $relatedModel, $forcedValues);

        if ($categories !== null) {
            $syncData = collect($categories)->mapWithKeys(fn($cat) => [
                $cat['category_id'] => array_filter(['relevance' => $cat['relevance'] ?? null])
            ])->toArray();
            $article->categories()->sync($syncData);
        }

        return $article;
    }

    /**
     * Override update to handle categories sync
     *
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $categories = $this->getAndUnset($data, 'categories');

        /** @var Article $article */
        $article = parent::update($model, $data, $forcedValues);

        if ($categories !== null) {
            $syncData = collect($categories)->mapWithKeys(fn($cat) => [
                $cat['category_id'] => array_filter(['relevance' => $cat['relevance'] ?? null])
            ])->toArray();
            $article->categories()->sync($syncData);
        }

        return $article;
    }
}