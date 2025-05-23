<?php
declare(strict_types=1);

namespace App\Policies\Statistic;

use App\Models\Role;
use App\Models\User\User;
use App\Athenia\Policies\BasePolicyAbstract;

/**
 * Class StatisticPolicy
 * @package App\Policies\Statistic
 */
class StatisticPolicy extends BasePolicyAbstract
{
    /**
     * Any user can index the statistics
     *
     * @param User $loggedInUser
     * @return bool
     */
    public function all(User $loggedInUser)
    {
        return true;
    }

    /**
     * Only content editors and support staff can view a statistic
     *
     * @param User $loggedInUser
     * @return bool
     */
    public function view(User $loggedInUser)
    {
        return $loggedInUser->hasRole([
            Role::CONTENT_EDITOR,
            Role::SUPPORT_STAFF,
        ]);
    }

    /**
     * Only logged in users can create new statistic filters
     *
     * @param User $loggedInUser
     * @return bool
     */
    public function create(User $loggedInUser)
    {
        return $loggedInUser->hasRole([
            Role::CONTENT_EDITOR,
        ]);
    }

    /**
     * Only logged in users can update statistic filters
     *
     * @param User $loggedInUser
     * @return bool
     */
    public function update(User $loggedInUser)
    {
        return $loggedInUser->hasRole([
            Role::CONTENT_EDITOR,
        ]);
    }

    /**
     * Only logged in users can delete statistic filters
     *
     * @param User $loggedInUser
     * @return bool
     */
    public function delete(User $loggedInUser)
    {
        return $loggedInUser->hasRole([
            Role::CONTENT_EDITOR,
        ]);
    }
} 