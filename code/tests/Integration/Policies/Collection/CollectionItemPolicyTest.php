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

final class CollectionItemPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllPassesWithPublicCollection(): void
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);

        $this->assertTrue($policy->all(new User(), $collection));
    }

    public function testAllBlocksDifferentUser(): void
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

    public function testAllBlocksUserOutOfOrganization(): void
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

    public function testAllPassesWithMatchingUser(): void
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

    public function testAllPassesInOrganization(): void
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

    public function testCreateBlocksDifferentUser(): void
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

    public function testCreateBlocksUserOutOfOrganization(): void
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

    public function testCreatePassesWithMatchingUser(): void
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

    public function testCreatePassesInOrganization(): void
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

    public function testViewFailsIdMismatch(): void
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create();

        $this->assertFalse($policy->view(new User(), $collection, $collectionItem));
    }

    public function testViewPassesWithPublicCollection(): void
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

    public function testViewBlocksDifferentUser(): void
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

    public function testViewBlocksUserOutOfOrganization(): void
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

    public function testViewPassesWithMatchingUser(): void
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

    public function testViewPassesInOrganization(): void
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

    public function testUpdateFailsIdMismatch(): void
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create();

        $this->assertFalse($policy->update(new User(), $collection, $collectionItem));
    }

    public function testUpdateBlocksDifferentUser(): void
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

    public function testUpdateBlocksUserOutOfOrganization(): void
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

    public function testUpdatePassesWithMatchingUser(): void
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

    public function testUpdatePassesInOrganization(): void
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

    public function testDeleteFailsIdMismatch(): void
    {
        $policy = new CollectionItemPolicy();

        $collection = Collection::factory()->create([
            'is_public' => true,
        ]);
        $collectionItem = CollectionItem::factory()->create();

        $this->assertFalse($policy->delete(new User(), $collection, $collectionItem));
    }

    public function testDeleteBlocksDifferentUser(): void
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

    public function testDeleteBlocksUserOutOfOrganization(): void
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

    public function testDeletePassesWithMatchingUser(): void
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

    public function testDeletePassesInOrganization(): void
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