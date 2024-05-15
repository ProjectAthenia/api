<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Collection;

use App\Models\Collection\Collection;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanDeleteTest
 * @package Tests\Feature\Http\Category
 */
final class CollectionDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $model = Collection::factory()->create();
        $response = $this->json('DELETE', '/v1/collections/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked(): void
    {
        $model = Collection::factory()->create();
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $response = $this->json('DELETE', '/v1/collections/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testDeleteSingle(): void
    {
        $this->actAsUser();

        $model = Collection::factory()->create([
            'owner_id' => $this->actingAs->id,
        ]);

        $response = $this->json('DELETE', '/v1/collections/' . $model->id);

        $response->assertStatus(204);
        $this->assertEquals(0, Collection::count());
    }

    public function testDeleteSingleInvalidIdFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/collections/a')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails(): void
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/collections/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}
