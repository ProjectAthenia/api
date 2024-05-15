<?php
declare(strict_types=1);

namespace App\Policies;

use App\Athenia\Contracts\BasePolicyContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RolePolicy
 * @package App\Policies
 */
class RolePolicy extends BasePolicyAbstract implements BasePolicyContract
{
    use HandlesAuthorization;

    /**
     * No one can see this besides super admins, which are already caught in the parent before
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user): bool
    {
        return false;
    }
}