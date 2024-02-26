<?php
declare(strict_types=1);

namespace App\Repositories\Collection;

use App\Contracts\Repositories\Collection\CollectionItemRepositoryContract;
use App\Models\Collection\CollectionItem;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

class CollectionItemRepository extends BaseRepositoryAbstract implements CollectionItemRepositoryContract
{
    /**
     * @param CollectionItem $model
     * @param LogContract $log
     */
    public function __construct(CollectionItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}