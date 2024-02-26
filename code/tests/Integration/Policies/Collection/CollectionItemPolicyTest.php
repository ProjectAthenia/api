<?php
declare(strict_types=1);

namespace Integration\Policies\Collection;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\User\User;
use App\Policies\Collection\CollectionItemPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

class CollectionItemPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllPassesWithPublicCollection()
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);

        $this->assertTrue($policy->all(new User(), $collection));
    }

    public function testAllBlocksDifferentUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);

        $this->assertFalse($policy->all($user, $collection));
    }

    public function testAllBlocksUserOutOfOrganization()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertFalse($policy->all($user, $collection));
    }

    public function testAllPassesWithMatchingUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->all($user, $collection));
    }

    public function testAllPassesInOrganization()
    {
        $policy = new CollectionItemPolicy();

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

        $this->assertTrue($policy->all($user, $collection));
    }

    public function testCreateBlocksDifferentUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);

        $this->assertFalse($policy->create($user, $collection));
    }

    public function testCreateBlocksUserOutOfOrganization()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);

        $this->assertFalse($policy->create($user, $collection));
    }

    public function testCreatePassesWithMatchingUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertTrue($policy->create($user, $collection));
    }

    public function testCreatePassesInOrganization()
    {
        $policy = new CollectionItemPolicy();

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

        $this->assertTrue($policy->create($user, $collection));
    }

    public function testViewFailsIdMismatch()
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create();

        $this->assertFalse($policy->view(new User(), $collection, $collectionItem));
    }

    public function testViewPassesWithPublicCollection()
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->view(new User(), $collection, $collectionItem));
    }

    public function testViewBlocksDifferentUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertFalse($policy->view($user, $collection, $collectionItem));
    }

    public function testViewBlocksUserOutOfOrganization()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertFalse($policy->view($user, $collection, $collectionItem));
    }

    public function testViewPassesWithMatchingUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->view($user, $collection, $collectionItem));
    }

    public function testViewPassesInOrganization()
    {
        $policy = new CollectionItemPolicy();

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
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->view($user, $collection, $collectionItem));
    }

    public function testUpdateFailsIdMismatch()
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create();

        $this->assertFalse($policy->update(new User(), $collection, $collectionItem));
    }

    public function testUpdateBlocksDifferentUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertFalse($policy->update($user, $collection, $collectionItem));
    }

    public function testUpdateBlocksUserOutOfOrganization()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertFalse($policy->update($user, $collection, $collectionItem));
    }

    public function testUpdatePassesWithMatchingUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->update($user, $collection, $collectionItem));
    }

    public function testUpdatePassesInOrganization()
    {
        $policy = new CollectionItemPolicy();

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
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->update($user, $collection, $collectionItem));
    }

    public function testDeleteFailsIdMismatch()
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create();

        $this->assertFalse($policy->delete(new User(), $collection, $collectionItem));
    }

    public function testDeleteBlocksDifferentUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $otherUser->id,
            'owner_type' => 'user',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertFalse($policy->delete($user, $collection, $collectionItem));
    }

    public function testDeleteBlocksUserOutOfOrganization()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $organization->id,
            'owner_type' => 'organization',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertFalse($policy->delete($user, $collection, $collectionItem));
    }

    public function testDeletePassesWithMatchingUser()
    {
        $policy = new CollectionItemPolicy();

        $user = User::factory()->create();

        $collection = Collection::factory()->create([
            'is_public' => false,
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->delete($user, $collection, $collectionItem));
    }

    public function testDeletePassesInOrganization()
    {
        $policy = new CollectionItemPolicy();

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
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($policy->delete($user, $collection, $collectionItem));
    }
}