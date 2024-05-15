<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Vote;

use App\Athenia\Contracts\Repositories\Vote\BallotItemOptionRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Vote\BallotItemOption;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class BallotItemOptionRepository
 * @package App\Repositories\Vote
 */
class BallotItemOptionRepository extends BaseRepositoryAbstract implements BallotItemOptionRepositoryContract
{
    /**
     * BallotItemOptionRepository constructor.
     * @param BallotItemOption $model
     * @param LogContract $log
     */
    public function __construct(BallotItemOption $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}
