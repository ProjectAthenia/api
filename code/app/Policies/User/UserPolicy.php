<?php
declare(strict_types=1);

namespace App\Policies\User;

use App\Athenia\Contracts\Models\HasPolicyContract;
use App\Athenia\Contracts\Policies\BasePolicyContract;
use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy
 * @package App\Policies\User
 */
class UserPolicy extends BasePolicyAbstract implements BasePolicyContract
{
    use HandlesAuthorization;

    /**
     * @var string can we view our self?
     */
    const ACTION_VIEW_SELF = 'view-self';

    /**
     * Determine if the user can view itself
     *
     * @param User $user
     * @return bool
     */
    public function viewSelf(User $user)
    {
        return true;
    }

    /**
     * Anyone can view a user
     *
     * @param User $user
     * @param HasPolicyContract $model
     * @return bool
     */
    public function view(User $user, HasPolicyContract $model)
    {
        return true;
    }

    /**
     * Anyone can view a user
     *
     * @param User $user
     * @param User|HasPolicyContract $model
     * @return bool
     */
    public function update(User $user, HasPolicyContract $model)
    {
        return $user->id == $model->id;
    }
}