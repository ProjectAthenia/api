<?php
declare(strict_types=1);

namespace Tests\Feature\V1\CollectionItem;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanDeleteTest
 * @package Tests\Feature\Http\Category
 */
class CollectionItemDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = CollectionItem::factory()->create();
        $response = $this->json('DELETE', '/v1/collection-items/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        $model = CollectionItem::factory()->create();
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $response = $this->json('DELETE', '/v1/collection-items/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testDeleteSingle()
    {
        $this->actAsUser();

        $model = CollectionItem::factory()->create([
            'collection_id' => Collection::factory()->create([
                'owner_id' => $this->actingAs->id,
            ])->id,
        ]);

        $response = $this->json('DELETE', '/v1/collection-items/' . $model->id);

        $response->assertStatus(204);
        $this->assertEquals(0, CollectionItem::count());
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/collection-items/a')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/collection-items/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}
