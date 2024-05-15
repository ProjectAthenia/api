<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Collection;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanUpdateTest
 * @package Tests\Feature\Http\Category
 */
final class CollectionUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/collections/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $model = Collection::factory()->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $model->id);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked(): void
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = Collection::factory()->create();
            $response = $this->json('PATCH', static::BASE_ROUTE . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testPatchSuccessful(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        /** @var Collection $model */
        $model = Collection::factory()->create([
            'name' => 'An Collection',
        ]);

        $data = [
            'name' => 'A Collection',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $model->id, $data);
        $response->assertStatus(200);
        $response->assertJson($data);

        /** @var Collection $updated */
        $updated = Collection::find($model->id);

        $this->assertEquals('A Collection', $updated->name);
    }

    public function testPatchSuccessfulWithNewOrder(): void
    {

        $this->actAs(Role::SUPER_ADMIN);

        /** @var Collection $model */
        $model = Collection::factory()->create();

        $collectionItems = CollectionItem::factory()->count(3)->create([
            'collection_id' => $model->id,
        ]);

        $data = [
            'collection_item_order' => [
                $collectionItems[1]->id,
                $collectionItems[0]->id,
                $collectionItems[2]->id,
            ],
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $model->id, $data);
        $response->assertStatus(200);

        /** @var Collection $updated */
        $updated = Collection::find($model->id);

        $this->assertCount(3, $updated->collectionItems);
        $this->assertEquals($collectionItems[1]->id, $updated->collectionItems[0]->id);
        $this->assertEquals($collectionItems[0]->id, $updated->collectionItems[1]->id);
        $this->assertEquals($collectionItems[2]->id, $updated->collectionItems[2]->id);
    }

    public function testPatchNotFoundFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchFailsInvalidStringFields(): void
    {
        $collection = Collection::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5,
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $collection->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidBooleanFields(): void
    {
        $collection = Collection::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'is_public' => 'hello',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $collection->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'is_public' => ['The is public field must be true or false.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidArrayFields(): void
    {
        $collection = Collection::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'collection_item_order' => 'hello',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $collection->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'collection_item_order' => ['The collection item order must be an array.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidIntegerFields(): void
    {
        $collection = Collection::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'collection_item_order' => [
                'hello',
            ],
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $collection->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'collection_item_order.0' => ['The collection_item_order.0 must be an integer.'],
            ]
        ]);
    }

    public function testPatchFailsModelsDoNotExistFields(): void
    {
        $collection = Collection::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'collection_item_order' => [
                45,
            ],
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $collection->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'collection_item_order.0' => ['The selected collection_item_order.0 is invalid.'],
            ]
        ]);
    }

    public function testPatchFailsUnrelatedCollectionItems(): void
    {
        $collection = Collection::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'collection_item_order' => [
                CollectionItem::factory()->create()->id,
            ],
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $collection->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'collection_item_order.0' => ['The collection_item_order.0 must be owned by the appropriate model.'],
            ]
        ]);
    }
}
