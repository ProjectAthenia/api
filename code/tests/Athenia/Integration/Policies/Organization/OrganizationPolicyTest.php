<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\Organization\OrganizationPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class OrganizationPolicyTest
 * @package Tests\Athenia\Integration\Policies\Organization
 */
final class OrganizationPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAll(): void
    {
        $policy = new OrganizationPolicy();

        $this->assertFalse($policy->all(new User()));
    }

    public function testCreate(): void
    {
        $policy = new OrganizationPolicy();

        $this->assertTrue($policy->create(new User()));
    }

    public function testViewBlocksWhenNotOrganizationManager(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($policy->view($user, $organization));
    }

    public function testViewPassesForOrganizationManager(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertTrue($policy->view($user, $organization));
    }

    public function testUpdateBlocksWhenNotOrganizationManager(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($policy->update($user, $organization));
    }

    public function testUpdatePassesForOrganizationManager(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertTrue($policy->update($user, $organization));
    }

    public function testDeleteBlocksWhenNotOrganizationManager(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($policy->delete($user, $organization));
    }

    public function testDeleteBlocksForOrganizationManager(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $this->assertFalse($policy->delete($user, $organization));
    }

    public function testDeletePassesForOrganizationAdmin(): void
    {
        $policy = new OrganizationPolicy();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        OrganizationManager::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertTrue($policy->delete($user, $organization));
    }
}
