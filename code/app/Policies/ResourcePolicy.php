<?php
declare(strict_types=1);

namespace App\Policies;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;

/**
 * Class ResourcePolicy
 * @package App\Policies
 */
class ResourcePolicy extends BasePolicyAbstract
{
    /**
     * Every logged in user can index resources
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return true;
    }
}