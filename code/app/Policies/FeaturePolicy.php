<?php
declare(strict_types=1);

namespace App\Policies;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Feature;
use App\Models\User\User;

/**
 * Class FeaturePolicy
 * @package App\Policies
 */
class FeaturePolicy extends BasePolicyAbstract
{
    /**
     * Only super admins can index features
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return true;
    }

    /**
     * Only super admins can view a feature
     *
     * @param User $user
     * @param Feature $feature
     * @return bool
     */
    public function view(User $user, Feature $feature)
    {
        return true;
    }
}
