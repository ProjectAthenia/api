<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Subscription;

use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Subscription\MembershipPlanRate;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class MembershipPlanRateRepository
 * @package App\Repositories\Subscription
 */
class MembershipPlanRateRepository extends BaseRepositoryAbstract implements MembershipPlanRateRepositoryContract
{
    /**
     * MembershipPlanRateRepository constructor.
     * @param MembershipPlanRate $model
     * @param LogContract $log
     */
    public function __construct(MembershipPlanRate $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}