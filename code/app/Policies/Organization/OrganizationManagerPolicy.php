<?php
declare(strict_types=1);

namespace App\Policies\Organization;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;

/**
 * Class OrganizationManagerPolicy
 * @package App\Policies\Organization
 */
class OrganizationManagerPolicy extends BasePolicyAbstract
{
    /**
     * Indexes all organization managers
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function all(User $user, Organization $organization)
    {
        return $user->canManageOrganization($organization, Role::MANAGER);
    }

    /**
     * Only organization admins can create new organization managers
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function create(User $user, Organization $organization)
    {
        return $user->canManageOrganization($organization, Role::ADMINISTRATOR);
    }

    /**
     * Only organization admins can update organization managers
     *
     * @param User $user
     * @param Organization $organization
     * @param OrganizationManager $organizationManager
     * @return bool
     */
    public function update(User $user, Organization $organization, OrganizationManager $organizationManager)
    {
        return $organization->id === $organizationManager->organization_id &&
            $user->canManageOrganization($organization, Role::ADMINISTRATOR);
    }

    /**
     * Only organization admins can delete organization managers
     *
     * @param User $user
     * @param Organization $organization
     * @param OrganizationManager $organizationManager
     * @return bool
     */
    public function delete(User $user, Organization $organization, OrganizationManager $organizationManager)
    {
        return $organization->id === $organizationManager->organization_id &&
            $user->canManageOrganization($organization, Role::ADMINISTRATOR);
    }
}