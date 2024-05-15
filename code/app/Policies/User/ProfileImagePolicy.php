<?php
declare(strict_types=1);

namespace App\Policies\User;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;

/**
 * Class ProfileImagePolicy
 * @package App\Policies\User
 */
class ProfileImagePolicy extends BasePolicyAbstract
{
    /**
     * Only admins can update a user
     *
     * @param User $loggedInUser
     * @param IsAnEntityContract $entity
     * @return bool
     */
    public function create(User $loggedInUser, IsAnEntityContract $entity)
    {
        return $entity->canUserManageEntity($loggedInUser);
    }
}