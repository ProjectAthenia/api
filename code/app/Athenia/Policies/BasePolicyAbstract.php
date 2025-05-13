<?php
declare(strict_types=1);

namespace App\Athenia\Policies;

use App\Athenia\Contracts\Policies\BasePolicyContract;
use App\Models\Role;
use App\Models\User\User;

/**
 * Class BasePolicyAbstract
 * @package App\Athenia\Policies
 */
abstract class BasePolicyAbstract implements BasePolicyContract
{
    /**
     * No one in this app should be able to see everything
     *
     * @param User $user
     * @return null|bool
     */
    public function before(User $user)
    {
        return $user->hasRole([Role::SUPER_ADMIN]) ?: null;
    }
} 