<?php
declare(strict_types=1);

namespace App\Athenia\Repositories;

use App\Athenia\Contracts\Repositories\RoleRepositoryContract;
use App\Models\Role;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class RoleRepository
 * @package App\Repositories
 */
class RoleRepository extends BaseRepositoryAbstract implements RoleRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Create,
        \App\Athenia\Repositories\Traits\NotImplemented\Update,
        \App\Athenia\Repositories\Traits\NotImplemented\FindOrFail,
        \App\Athenia\Repositories\Traits\NotImplemented\Delete;

    /**
     * RoleRepository constructor.
     * @param Role $role
     * @param LogContract $log
     */
    public function __construct(Role $role, LogContract $log)
    {
        parent::__construct($role, $log);
    }
}