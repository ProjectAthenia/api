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
 * Class MembershipPlanUpdateTest
 * @package Tests\Feature\Http\Category
 */
class CategoryUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/categories/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $category = Category::factory()->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $category->id);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $category = Category::factory()->create();
            $response = $this->json('PATCH', static::BASE_ROUTE . $category->id);
            $response->assertStatus(403);
        }
    }

    public function testPatchSuccessful()
    {
        $this->actAs(Role::SUPER_ADMIN);

        /** @var Category $category */
        $category = Category::factory()->create([
            'name' => 'An Category',
        ]);

        $data = [
            'name' => 'A Category',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $category->id, $data);
        $response->assertStatus(200);
        $response->assertJson($data);

        /** @var Category $updated */
        $updated = Category::find($category->id);

        $this->assertEquals('A Category', $updated->name);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchFailsInvalidStringFields()
    {
        $category = Category::factory()->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5,
            'description' => 5,
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $category->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
                'description' => ['The description must be a string.'],
            ]
        ]);
    }
}
