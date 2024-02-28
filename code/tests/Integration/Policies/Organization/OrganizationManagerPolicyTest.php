<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\Organization\OrganizationManagerPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class OrganizationManagerPolicyTest
 * @package Tests\Integration\Policies\Organization
 */
class OrganizationManagerPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllBlocksWhenNotOrganizationManager(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($policy->all($user, $organization));
    }

    public function testAllPassesForOrganizationManager(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertTrue($policy->all($user, $organization));
    }

    public function testCreateBlocksWhenNotOrganizationManager(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($policy->create($user, $organization));
    }

    public function testCreateBlocksForOrganizationManager(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertFalse($policy->create($user, $organization));
    }

    public function testCreatePassesForOrganizationAdmin(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertTrue($policy->create($user, $organization));
    }

    public function testUpdateBlocksWithOrganizationMismatch(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organizationManager = OrganizationManager::factory()->create();

        $this->assertFalse($policy->update($user, $organization, $organizationManager));
    }

    public function testUpdateBlocksWhenNotOrganizationNot(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organizationManager = OrganizationManager::factory()->create([
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertFalse($policy->update($user, $organization, $organizationManager));
    }

    public function testUpdateBlocksForOrganizationManager(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organizationManager = OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertFalse($policy->update($user, $organization, $organizationManager));
    }

    public function testUpdatePassesForOrganizationAdmin(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organizationManager = OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertTrue($policy->update($user, $organization, $organizationManager));
    }

    public function testDeleteBlocksWithOrganizationMismatch(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organizationManager = OrganizationManager::factory()->create();

        $this->assertFalse($policy->delete($user, $organization, $organizationManager));
    }

    public function testDeleteBlocksWhenNotOrganizationNot(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organizationManager = OrganizationManager::factory()->create([
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertFalse($policy->delete($user, $organization, $organizationManager));
    }

    public function testDeleteBlocksForOrganizationManager(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organizationManager = OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertFalse($policy->delete($user, $organization, $organizationManager));
    }

    public function testDeletePassesForOrganizationAdmin(): void
    {
        $policy = new OrganizationManagerPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organizationManager = OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertTrue($policy->delete($user, $organization, $organizationManager));
    }
}
