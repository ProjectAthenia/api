<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Category;

use App\Models\Category;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanDeleteTest
 * @package Tests\Feature\Http\Category
 */
class CategoryDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = Category::factory()->create();
        $response = $this->json('DELETE', '/v1/categories/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        $model = Category::factory()->create();
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $response = $this->json('DELETE', '/v1/categories/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testDeleteSingle()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $model = Category::factory()->create();

        $response = $this->json('DELETE', '/v1/categories/' . $model->id);

        $response->assertStatus(204);
        $this->assertEquals(0, Category::count());
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/categories/a')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/categories/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}
