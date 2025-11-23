<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\User;

use App\Athenia\Contracts\Repositories\User\ArticleNoteRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\User\ArticleNote;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleNoteRepository
 * @package App\Athenia\Repositories\User
 */
class ArticleNoteRepository extends BaseRepositoryAbstract implements ArticleNoteRepositoryContract
{
    use CanGetAndUnset;

    /**
     * ArticleNoteRepository constructor.
     * @param ArticleNote $model
     * @param LogContract $log
     */
    public function __construct(ArticleNote $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * Override create to handle completed boolean
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = []): BaseModelAbstract
    {
        $completed = $this->getAndUnset($data, 'completed');

        if ($completed === true) {
            $data['completed_at'] = now();
        }

        return parent::create($data, $relatedModel, $forcedValues);
    }

    /**
     * Override update to handle completed boolean
     *
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $completed = $this->getAndUnset($data, 'completed');

        if ($completed === true) {
            $data['completed_at'] = now();
        } elseif ($completed === false) {
            $data['completed_at'] = null;
        }

        return parent::update($model, $data, $forcedValues);
    }
}
