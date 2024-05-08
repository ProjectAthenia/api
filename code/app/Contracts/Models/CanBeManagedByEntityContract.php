<?php
declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\User\User;

interface CanBeManagedByEntityContract
{
    /**
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @param string $action
     * @return bool
     */
    public function canUserManage(User $loggedInUser, IsAnEntityContract $entity, string $action): bool;
}