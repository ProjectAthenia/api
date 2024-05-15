<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Organization;

use App\Athenia\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Organization\Organization;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class OrganizationRepository
 * @package App\Repositories\Organization
 */
class OrganizationRepository extends BaseRepositoryAbstract implements OrganizationRepositoryContract
{
    /**
     * OrganizationRepository constructor.
     * @param Organization $model
     * @param LogContract $log
     */
    public function __construct(Organization $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}