<?php
declare(strict_types=1);

namespace Integration\Policies\Collection;

use App\Models\Collection\Collection;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\User\User;
use App\Policies\Collection\CollectionPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

class CollectionPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllPasses()
    {
        $policy = new CollectionPolicy();

        $this->assertTrue($policy->all(new User()));
    }

    public function testCreateBlocksDifferentUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->assertFalse($policy->create($user, $otherUser));
    }

    public function testCreateBlocksUserOutOfOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->assertFalse($policy->create($user, $organization));
    }

    public function testCreatePassesWithMatchingUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();

        $this->assertTrue($policy->create($user, $user));
    }

    public function testCreatesPassesInOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        OrganizationManager::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertTrue($policy->create($user, $organization));
    }

    public function testViewPassesWithPublicCollection()
    {
        $policy = new CollectionPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);

        $this->assertTrue($policy->view(new User(), $collection));
    }

    public function testViewBlocksDifferentUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);

        $this->assertFalse($policy->view($user, $collection));
    }

    public function testViewBlocksUserOutOfOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertFalse($policy->view($user, $collection));
    }

    public function testViewPassesWithMatchingUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->view($user, $collection));
    }

    public function testViewPassesInOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        OrganizationManager::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertTrue($policy->view($user, $collection));
    }

    public function testUpdateBlocksDifferentUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);

        $this->assertFalse($policy->update($user, $collection));
    }

    public function testUpdateBlocksUserOutOfOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertFalse($policy->update($user, $collection));
    }

    public function testUpdatePassesWithMatchingUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->update($user, $collection));
    }

    public function testUpdatePassesInOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        OrganizationManager::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertTrue($policy->update($user, $collection));
    }

    public function testDeleteBlocksDifferentUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);

        $this->assertFalse($policy->delete($user, $collection));
    }

    public function testDeleteBlocksUserOutOfOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertFalse($policy->delete($user, $collection));
    }

    public function testDeletePassesWithMatchingUser()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->delete($user, $collection));
    }

    public function testDeletePassesInOrganization()
    {
        $policy = new CollectionPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        OrganizationManager::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertTrue($policy->delete($user, $collection));
    }
}