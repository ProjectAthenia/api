<?php
declare(strict_types=1);

namespace App\Policies\Organization;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Organization\Organization;
use App\Models\Role;
use App\Models\User\User;

/**
 * Class OrganizationPolicy
 * @package App\Policies\Organization
 */
class OrganizationPolicy extends BasePolicyAbstract
{
    /**
     * Only Super Admins can view all organizations
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return false;
    }

    /**
     * All users can create organizations
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Organization managers and admins can view
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function view(User $user, Organization $organization)
    {
        return $user->canManageOrganization($organization, Role::MANAGER);
    }

    /**
     * Organization managers and admins can update
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function update(User $user, Organization $organization)
    {
        return $user->canManageOrganization($organization, Role::MANAGER);
    }

    /**
     * Only admins can delete an organization
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function delete(User $user, Organization $organization)
    {
        return $user->canManageOrganization($organization, Role::ADMINISTRATOR);
    }
}