<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Organization;

use App\Athenia\Contracts\Repositories\Organization\OrganizationManagerRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Organization\OrganizationManager;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class OrganizationManagerRepository
 * @package App\Repositories\Organization
 */
class OrganizationManagerRepository extends BaseRepositoryAbstract implements OrganizationManagerRepositoryContract
{
    /**
     * OrganizationManagerRepository constructor.
     * @param OrganizationManager $model
     * @param LogContract $log
     */
    public function __construct(OrganizationManager $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}