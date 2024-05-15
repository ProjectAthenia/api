<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Collection;

use App\Athenia\Contracts\Repositories\Collection\CollectionItemRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Collection\CollectionItem;
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