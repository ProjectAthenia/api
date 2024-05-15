<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Payment;

use App\Athenia\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Payment\LineItem;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class LineItemRepository
 * @package App\Repositories\Payment
 */
class LineItemRepository extends BaseRepositoryAbstract implements LineItemRepositoryContract
{
    /**
     * LineItemRepository constructor.
     * @param LineItem $model
     * @param LogContract $log
     */
    public function __construct(LineItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}